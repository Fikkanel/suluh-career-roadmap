<?php

namespace App\Listeners;

use App\Events\CareerChosen;
use App\Services\ContextScoreService;

class UpdateContextScoreFromCareerChosen
{
    public function __construct(
        private readonly ContextScoreService $contextScore,
    ) {}

    public function handle(CareerChosen $event): void
    {
        $user = $event->user;
        $existing = $user->contextScore?->factors ?? [];
        $behavior = $existing['behavior_signals'] ?? [];
        $behavior['career_chosen_at'] = now()->toDateTimeString();
        $behavior['career_chosen_count'] = ($behavior['career_chosen_count'] ?? 0) + 1;

        $scoreData = $this->contextScore->calculateFromOnboarding(array_merge(
            $user->only(['work_experience', 'education_level', 'exploration_readiness', 'support_level']),
            ['behavior_signals' => $behavior]
        ));

        $this->contextScore->updateForUser($user->id, $scoreData);
        $user->load('contextScore');
    }
}
