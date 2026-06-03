<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Eloquent\ProgressRepository;
use App\Models\UserProgress;
use App\Services\LLMNarrativeService;

class DashboardController extends Controller
{
    public function __construct(
        private readonly ProgressRepository  $progressRepo,
        private readonly LLMNarrativeService $llm,
    ) {}

    public function index()
    {
        $user   = auth()->user();
        
        if ($user->is_admin) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'mentor') {
            return redirect()->route('mentor.dashboard');
        }
        
        $career = $user->currentCareer;

        // The 'assessed' middleware guarantees $career exists for regular users.
        // This fallback is a safety net only.
        if (! $career) {
            return redirect()->route('assessment.result')
                ->with('info', 'Pilih karir dari hasil asesmen untuk melanjutkan.');
        }

        $crs             = $this->progressRepo->calculateCrs($user->id, $career->id);
        $allProgress     = $this->progressRepo->getByUser($user->id);
        $completedSkills = collect($allProgress)->where('status', 'done')->count();
        $totalSkills     = collect($allProgress)->count();

        $nextSkill = collect($allProgress)
            ->where('status', '!=', 'done')
            ->sortBy(fn ($p) => $p['skill']['order'] ?? 99)
            ->first();

        $lastActivity = UserProgress::where('user_id', $user->id)
            ->latest('updated_at')
            ->first()?->updated_at;

        $milestoneNarrative = null;
        foreach ([100, 75, 50, 25] as $milestone) {
            if ($crs >= $milestone) {
                $milestoneNarrative = $this->llm->generate($user->id, 'milestone_' . $milestone, [
                    'career' => $career->name,
                    'crs'    => $crs,
                ]);
                break;
            }
        }

        // Generate Job Recommendations using cooperative AI
        $jobs = [];
        if ($crs >= 15) {
            $jobsJson = $this->llm->generate($user->id, 'job_recommendations', [
                'career'   => $career->name,
                'province' => $user->province ?? 'DKI Jakarta',
                'crs'      => $crs,
            ]);

            try {
                $jobs = json_decode($jobsJson, true);
                if (!is_array($jobs)) {
                    $jobs = [];
                }
            } catch (\Throwable $e) {
                $jobs = [];
            }

            if (empty($jobs)) {
                $jobs = [
                    [
                        'title' => 'Junior ' . $career->name,
                        'company' => 'Perusahaan Mitra Suluh',
                        'location' => $user->province ?? 'Jakarta',
                        'salary' => 'Rp 6.000.000 - Rp 8.000.000',
                        'match' => min(95, $crs + 15) . '%',
                        'source' => 'Suluh Network'
                    ],
                    [
                        'title' => $career->name . ' Magang',
                        'company' => 'Startup Lokal',
                        'location' => 'Remote',
                        'salary' => 'Rp 3.000.000 - Rp 4.500.000',
                        'match' => min(98, $crs + 25) . '%',
                        'source' => 'Suluh Network'
                    ]
                ];
            }
        }

        // Mentor Feedbacks
        $mentorFeedbacks = \App\Models\MentorFeedback::where('user_id', $user->id)
            ->with('mentor')
            ->latest()
            ->take(3)
            ->get();

        $data = [
            'crs'                 => $crs,
            'career'              => $career->name,
            'career_id'           => $career->id,
            'roadmap_stage'       => $this->detectStage($crs),
            'next_skill'          => $nextSkill['skill']['name'] ?? null,
            'completed_skills'    => $completedSkills,
            'total_skills'        => $totalSkills,
            'last_activity'       => $lastActivity ? $lastActivity->diffForHumans() : 'Baru saja',
            'milestone_narrative' => $milestoneNarrative,
            'job_recommendations' => $jobs,
            'mentor_feedbacks'    => $mentorFeedbacks,
        ];

        return view('app.dashboard', compact('data'));
    }

    private function detectStage(int $crs): string
    {
        return match (true) {
            $crs >= 75 => 'Lanjutan',
            $crs >= 40 => 'Menengah',
            default    => 'Pemula',
        };
    }
}
