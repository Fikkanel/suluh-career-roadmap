<?php

namespace App\Events;

use App\Models\User;
use App\Models\Career;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CareerChosen
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly Career $career,
    ) {}
}
