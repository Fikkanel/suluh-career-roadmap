<?php

namespace App\Events;

use App\Models\User;
use App\Models\Skill;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SkillStatusUpdated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly Skill $skill,
        public readonly string $oldStatus,
        public readonly string $newStatus,
    ) {}
}
