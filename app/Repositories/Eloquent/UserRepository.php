<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function findById(int $id)
    {
        return User::findOrFail($id);
    }

    public function updatePersonalityScores(int $userId, array $scores): bool
    {
        // TODO Slice 2: implement with encryption
        return User::where('id', $userId)->update(['personality_scores' => $scores]);
    }

    public function updateOnboarding(int $userId, array $data): bool
    {
        return User::where('id', $userId)->update([
            'age_range'       => $data['age_range']       ?? null,
            'education_level' => $data['education_level'] ?? null,
            'work_experience' => $data['work_experience'] ?? null,
        ]);
    }
}
