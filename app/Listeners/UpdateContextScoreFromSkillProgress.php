<?php

namespace App\Listeners;

use App\Events\SkillStatusUpdated;
use App\Services\ContextScoreService;

class UpdateContextScoreFromSkillProgress
{
    public function __construct(
        private readonly ContextScoreService $contextScore,
    ) {}

    public function handle(SkillStatusUpdated $event): void
    {
        $user = $event->user;
        $existing = $user->contextScore?->factors ?? [];
        $behavior = $existing['behavior_signals'] ?? [];

        // Track skill progress behavior
        $behavior['skills_progressed'] = ($behavior['skills_progressed'] ?? 0) + 1;
        $behavior['last_skill_update'] = now()->toDateTimeString();

        // Track consistency (skills completed)
        if ($event->newStatus === 'done') {
            $behavior['skills_completed'] = ($behavior['skills_completed'] ?? 0) + 1;
        }

        $scoreData = $this->contextScore->calculateFromOnboarding(array_merge(
            $user->only(['work_experience', 'education_level', 'exploration_readiness', 'support_level']),
            ['behavior_signals' => $behavior]
        ));

        $this->contextScore->updateForUser($user->id, $scoreData);
        $user->load('contextScore');
    }
}
