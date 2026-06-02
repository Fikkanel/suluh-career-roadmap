<?php

namespace App\Repositories\Eloquent;

use App\Models\UserProgress;
use App\Models\Skill;
use App\Events\SkillStatusUpdated;
use App\Repositories\Contracts\ProgressRepositoryInterface;

class ProgressRepository implements ProgressRepositoryInterface
{
    public function getByUser(int $userId): array
    {
        return UserProgress::where('user_id', $userId)->with('skill')->get()->toArray();
    }

    public function updateStatus(int $userId, int $skillId, string $status): bool
    {
        $progress = UserProgress::firstOrCreate(
            ['user_id' => $userId, 'skill_id' => $skillId],
            ['status' => 'not_started']
        );
        $oldStatus = $progress->status;
        $progress->status = $status;
        if ($status === 'learning' && !$progress->started_at) {
            $progress->started_at = now();
        }
        if ($status === 'done') {
            $progress->completed_at = now();
        }
        $saved = $progress->save();

        // Dispatch event for dynamic context score
        if ($saved && $oldStatus !== $status) {
            $user = \App\Models\User::find($userId);
            $skill = Skill::find($skillId);
            if ($user && $skill) {
                SkillStatusUpdated::dispatch($user, $skill, $oldStatus, $status);
            }
        }

        return $saved;
    }

    public function calculateCrs(int $userId, int $careerId): int
    {
        $total = Skill::where('career_id', $careerId)->count();
        if ($total === 0) return 0;
        $done  = UserProgress::where('user_id', $userId)
            ->whereHas('skill', fn ($q) => $q->where('career_id', $careerId))
            ->where('status', 'done')
            ->count();
        return (int) round($done / $total * 100);
    }
}
