<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserProgress;

class ProgressPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, UserProgress $userProgress): bool
    {
        return $user->id === $userProgress->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, UserProgress $userProgress): bool
    {
        return $user->id === $userProgress->user_id;
    }

    public function delete(User $user, UserProgress $userProgress): bool
    {
        return $user->id === $userProgress->user_id;
    }

    public function restore(User $user, UserProgress $userProgress): bool
    {
        return $user->id === $userProgress->user_id;
    }

    public function forceDelete(User $user, UserProgress $userProgress): bool
    {
        return $user->id === $userProgress->user_id;
    }
}
