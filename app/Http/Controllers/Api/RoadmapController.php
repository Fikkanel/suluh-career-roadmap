<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Eloquent\ProgressRepository;

class RoadmapController extends Controller
{
    public function __construct(
        private readonly ProgressRepository $progressRepo,
    ) {}

    public function current()
    {
        $user   = auth()->user();
        $career = $user->currentCareer;

        if (! $career) {
            return response()->json(['message' => 'Belum memilih karir.'], 404);
        }

        $allProgress = collect($this->progressRepo->getByUser($user->id));

        $levelOrder = ['beginner' => 0, 'intermediate' => 1, 'advanced' => 2];
        $grouped = $allProgress
            ->sortBy(fn ($p) => [$levelOrder[$p['skill']['level'] ?? 'beginner'] ?? 0, $p['skill']['order'] ?? 99])
            ->groupBy(fn ($p) => $p['skill']['level'] ?? 'beginner');

        $stages = $grouped->map(function ($items, $level) use ($career) {
            $levelDone  = $items->where('status', 'done')->count();
            $levelTotal = $items->count();
            $stageStatus = match (true) {
                $levelDone === $levelTotal => 'done',
                $levelDone > 0            => 'in_progress',
                default                   => 'not_started',
            };

            return [
                'level'       => $level,
                'status'      => $stageStatus,
                'skill_count' => $levelTotal,
                'completed'   => $levelDone,
                'skills'      => $items->map(fn ($p) => [
                    'skill_id' => $p['skill_id'],
                    'name'     => $p['skill']['name'] ?? '—',
                    'status'   => $p['status'],
                ])->values()->toArray(),
            ];
        })->values()->toArray();

        return response()->json([
            'career' => $career->only(['id', 'name', 'riasec_code']),
            'stages' => $stages,
        ]);
    }
}
