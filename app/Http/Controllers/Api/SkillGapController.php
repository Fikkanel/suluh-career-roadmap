<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Eloquent\ProgressRepository;
use App\Services\ScoringService;

class SkillGapController extends Controller
{
    public function __construct(
        private readonly ProgressRepository $progressRepo,
        private readonly ScoringService    $scoring,
    ) {}

    public function show()
    {
        $user   = auth()->user();
        $career = $user->currentCareer;

        if (! $career) {
            return response()->json(['message' => 'Belum memilih karir.'], 404);
        }

        $progressRows = $this->progressRepo->getByUser($user->id);
        $crs = $this->progressRepo->calculateCrs($user->id, $career->id);

        $skills = collect($progressRows)->map(fn ($p) => [
            'skill_id'     => $p['skill_id'],
            'name'         => $p['skill']['name'] ?? '—',
            'level'        => $p['skill']['level'] ?? 'beginner',
            'status'       => $p['status'],
            'transferable' => (bool) ($p['skill']['is_transferable'] ?? false),
        ])->values()->toArray();

        $gaps = collect($progressRows)
            ->filter(fn ($p) => $p['status'] !== 'done')
            ->map(fn ($p) => [
                'skill_id' => $p['skill_id'],
                'name'     => $p['skill']['name'] ?? '—',
                'level'    => $p['skill']['level'] ?? 'beginner',
                'status'   => $p['status'],
            ])->values()->toArray();

        return response()->json([
            'career_readiness_score' => $crs,
            'total_skills'  => count($skills),
            'completed'     => count($skills) - count($gaps),
            'gaps'          => $gaps,
            'all_skills'    => $skills,
        ]);
    }
}
