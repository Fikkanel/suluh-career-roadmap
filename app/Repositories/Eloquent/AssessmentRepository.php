<?php

namespace App\Repositories\Eloquent;

use App\Models\AssessmentQuestion;
use App\Models\AssessmentResult;
use App\Repositories\Contracts\AssessmentRepositoryInterface;

class AssessmentRepository implements AssessmentRepositoryInterface
{
    public function getActiveQuestions(): array
    {
        return AssessmentQuestion::where('is_active', true)
            ->orderBy('order')
            ->get()
            ->toArray();
    }

    public function saveResult(int $userId, array $scores, array $topCareerIds): mixed
    {
        return AssessmentResult::create([
            'user_id'        => $userId,
            'riasec_scores'  => $scores['riasec']   ?? [],
            'big_five_scores'=> $scores['big_five']  ?? [],
            'top_career_ids' => $topCareerIds,
            'crs'            => 0,
        ]);
    }

    public function getLatestResult(int $userId): mixed
    {
        return AssessmentResult::where('user_id', $userId)
            ->latest()
            ->first();
    }
}
