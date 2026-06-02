<?php

namespace App\Services;

use App\Models\Career;
use App\Models\Skill;
use App\Models\UserProgress;

class RoadmapGeneratorService
{
    /**
     * Generate a roadmap for a user given a career, taking into account
     * skills they already completed in previous roadmaps.
     * TODO Slice 2: implement full generation logic with transferable skill recognition.
     */
    public function generate(int $userId, int $careerId): array
    {
        $career = Career::with('skills')->findOrFail($careerId);
        $existingDone = UserProgress::where('user_id', $userId)
            ->where('status', 'done')
            ->pluck('skill_id')
            ->toArray();

        $stages = [];
        foreach ($career->skills as $skill) {
            $stages[] = [
                'skill_id'    => $skill->id,
                'name'        => $skill->name,
                'level'       => $skill->level,
                'status'      => in_array($skill->id, $existingDone) ? 'done' : 'not_started',
                'transferred' => in_array($skill->id, $existingDone),
            ];
        }

        return $stages;
    }
}
