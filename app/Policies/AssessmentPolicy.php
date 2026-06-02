<?php

namespace App\Policies;

use App\Models\AssessmentResult;
use App\Models\User;

class AssessmentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, AssessmentResult $assessmentResult): bool
    {
        return $user->id === $assessmentResult->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, AssessmentResult $assessmentResult): bool
    {
        return $user->id === $assessmentResult->user_id;
    }

    public function delete(User $user, AssessmentResult $assessmentResult): bool
    {
        return $user->id === $assessmentResult->user_id;
    }

    public function restore(User $user, AssessmentResult $assessmentResult): bool
    {
        return $user->id === $assessmentResult->user_id;
    }

    public function forceDelete(User $user, AssessmentResult $assessmentResult): bool
    {
        return $user->id === $assessmentResult->user_id;
    }
}
