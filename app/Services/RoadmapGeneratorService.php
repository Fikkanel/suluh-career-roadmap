<?php

namespace App\Services;

use App\Models\Career;
use App\Models\Skill;
use App\Models\UserProgress;
use App\Models\User;

class RoadmapGeneratorService
{
    public function __construct(
        private readonly LLMNarrativeService $llmService
    ) {}

    /**
     * Generate a roadmap for a user given a career, taking into account
     * skills they already completed in previous roadmaps.
     */
    public function generate(int $userId, int $careerId): array
    {
        $career = Career::with('skills')->findOrFail($careerId);

        if ($career->skills->isEmpty()) {
            $user = User::find($userId);
            $jsonString = $this->llmService->generate($userId, 'roadmap_generation', [
                'career_name' => $career->name,
                'major'       => $user ? $user->major : 'Umum'
            ]);

            $decoded = json_decode($jsonString, true);
            if (is_array($decoded)) {
                foreach ($decoded as $skillData) {
                    Skill::create([
                        'career_id'         => $career->id,
                        'name'              => $skillData['name'] ?? 'Skill Baru',
                        'level'             => $skillData['level'] ?? 'beginner',
                        'estimated_hours'   => $skillData['estimated_hours'] ?? 10,
                        'order'             => $skillData['order'] ?? 1,
                        'validation_type'   => $skillData['validation_type'] ?? 'none',
                        'scenario_question' => $skillData['scenario_question'] ?? null,
                    ]);
                }
                // Reload skills relation
                $career->load('skills');
            }
        }

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

