<?php

namespace App\Listeners;

use App\Events\UserLoggedIn;
use App\Services\ContextScoreService;

class UpdateContextScoreFromLogin
{
    public function __construct(
        private readonly ContextScoreService $contextScore,
    ) {}

    public function handle(UserLoggedIn $event): void
    {
        $user = $event->user;
        $existing = $user->contextScore?->factors ?? [];
        $behavior = $existing['behavior_signals'] ?? [];

        // Track return frequency
        $behavior['login_count'] = ($behavior['login_count'] ?? 0) + 1;
        $behavior['last_login'] = now()->toDateTimeString();

        // Update last_login_at on user
        $user->update(['last_login_at' => now()]);

        $scoreData = $this->contextScore->calculateFromOnboarding(array_merge(
            $user->only(['work_experience', 'education_level', 'exploration_readiness', 'support_level']),
            ['behavior_signals' => $behavior]
        ));

        $this->contextScore->updateForUser($user->id, $scoreData);
        $user->load('contextScore');
    }
}
