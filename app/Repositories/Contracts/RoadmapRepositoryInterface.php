<?php

namespace App\Repositories\Contracts;

interface RoadmapRepositoryInterface
{
    public function getCurrentRoadmap(int $userId): mixed;
    public function archiveRoadmap(int $userId, string $reflection): mixed;
    public function getArchives(int $userId): array;
}
