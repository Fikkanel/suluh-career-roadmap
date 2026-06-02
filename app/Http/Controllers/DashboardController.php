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

        if (! $career) {
            return redirect()->route('assessment')
                ->with('info', 'Lengkapi asesmen untuk membuka dashboard personalisasimu.');
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

        // Generate Dummy Job Recommendations (Fase 2 MVP)
        $jobs = [];
        if ($crs >= 15) { // Hanya tampilkan jika sudah ada progres
            $jobs = [
                [
                    'title' => 'Junior ' . $career->name,
                    'company' => 'TechCorp Indonesia',
                    'location' => 'Jakarta Selatan (Hybrid)',
                    'salary' => 'Rp 6.000.000 - Rp 8.000.000',
                    'match' => min(95, $crs + 15) . '%',
                    'source' => 'Glints'
                ],
                [
                    'title' => $career->name . ' Intern',
                    'company' => 'StartupJuara',
                    'location' => 'Remote',
                    'salary' => 'Rp 3.000.000 - Rp 4.500.000',
                    'match' => min(98, $crs + 25) . '%',
                    'source' => 'Jobstreet'
                ]
            ];
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
