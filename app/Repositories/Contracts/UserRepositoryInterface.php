<?php

namespace App\Repositories\Contracts;

interface UserRepositoryInterface
{
    public function findById(int $id);
    public function updatePersonalityScores(int $userId, array $scores): bool;
    public function updateOnboarding(int $userId, array $data): bool;
}
