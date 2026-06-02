<?php

namespace App\Repositories\Eloquent;

use App\Models\ImpactSurvey;
use App\Models\User;
use App\Repositories\Contracts\ImpactRepositoryInterface;

class ImpactRepository implements ImpactRepositoryInterface
{
    public function getAggregateStats(): array
    {
        // TODO Slice 2: compute real aggregates, cache for 24h
        return [
            'total_users'          => User::count(),
            'avg_crs_improvement'  => 38,
            'positive_change_pct'  => 62,
            'pivot_rate'           => 23,
        ];
    }

    public function recordSurvey(int $userId, string $type, array $answers): mixed
    {
        return ImpactSurvey::create([
            'user_id'      => $userId,
            'type'         => $type,
            'answers'      => $answers,
            'submitted_at' => now(),
        ]);
    }
}
