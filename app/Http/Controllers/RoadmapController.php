<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Eloquent\ProgressRepository;
use App\Services\LLMNarrativeService;

class RoadmapController extends Controller
{
    public function __construct(
        private readonly ProgressRepository $progressRepo,
        private readonly LLMNarrativeService $llm,
    ) {}

    public function index()
    {
        $user   = auth()->user();
        $career = $user->currentCareer;

        if (! $career) {
            return redirect()->route('assessment.result')
                ->with('info', 'Pilih karir dulu sebelum melihat roadmap.');
        }

        $allProgress = collect($this->progressRepo->getByUser($user->id));

        $levelOrder = ['beginner' => 0, 'intermediate' => 1, 'advanced' => 2];

        $grouped = $allProgress
            ->sortBy(fn ($p) => [$levelOrder[$p['skill']['level'] ?? 'beginner'] ?? 0, $p['skill']['order'] ?? 99])
            ->groupBy(fn ($p) => $p['skill']['level'] ?? 'beginner');

        $stages = $grouped->map(function ($items, $level) use ($career) {
            $levelDone   = $items->where('status', 'done')->count();
            $levelTotal  = $items->count();

            $stageStatus = match (true) {
                $levelDone === $levelTotal              => 'done',
                $levelDone > 0                         => 'in_progress',
                default                                => 'not_started',
            };

            $nextSkill = $items->where('status', '!=', 'done')
                ->sortBy(fn ($p) => $p['skill']['order'] ?? 99)
                ->first();

            $levelLabels = ['beginner' => 'Fondasi', 'intermediate' => 'Menengah', 'advanced' => 'Lanjutan'];
            $durationMap = ['beginner' => '4–8 minggu', 'intermediate' => '8–12 minggu', 'advanced' => '12–18 minggu'];

            return [
                'title'      => ($levelLabels[$level] ?? ucfirst($level)) . ' — ' . $career->name,
                'level'      => $level,
                'duration'   => $durationMap[$level] ?? '',
                'status'     => $stageStatus,
                'skillCount' => $levelTotal,
                'description'=> $levelDone . ' dari ' . $levelTotal . ' skill selesai.',
                'nextAction' => $nextSkill ? 'Lanjutkan: ' . ($nextSkill['skill']['name'] ?? '') : null,
            ];
        })->values()->toArray();

        $dimensions   = ['Fondasi' => 'Dasar', 'Menengah' => 'Menengah', 'Lanjutan' => 'Lanjutan'];
        $userValues   = [];
        $targetValues = [];
        foreach (['beginner' => 'Fondasi', 'intermediate' => 'Menengah', 'advanced' => 'Lanjutan'] as $lvl => $label) {
            $lvlItems    = $allProgress->filter(fn ($p) => ($p['skill']['level'] ?? '') === $lvl);
            $lvlTotal    = $lvlItems->count();
            $lvlDone     = $lvlItems->where('status', 'done')->count();
            $userValues[$label]   = $lvlTotal > 0 ? (int) round($lvlDone / $lvlTotal * 100) : 0;
            $targetValues[$label] = 100;
        }

        $completedSkills = $allProgress->where('status', 'done')->count();
        $totalSkills     = $allProgress->count();
        $crs             = $totalSkills > 0 ? (int) round($completedSkills / $totalSkills * 100) : 0;

        $guidance = $this->llm->generate($user->id, 'roadmap_guidance', [
            'career'           => $career->name,
            'crs'              => $crs,
            'completed_skills' => $completedSkills,
            'total_skills'     => $totalSkills,
            'major'            => $user->major ?? 'Umum',
        ]);

        return view('app.roadmap', [
            'career'       => $career->name,
            'stages'       => $stages,
            'dimensions'   => $dimensions,
            'userValues'   => $userValues,
            'targetValues' => $targetValues,
            'guidance'     => $guidance,
        ]);
    }
}
