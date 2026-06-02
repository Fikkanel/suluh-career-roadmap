<?php

namespace App\Repositories\Contracts;

interface ProgressRepositoryInterface
{
    public function getByUser(int $userId): array;
    public function updateStatus(int $userId, int $skillId, string $status): bool;
    public function calculateCrs(int $userId, int $careerId): int;
}
