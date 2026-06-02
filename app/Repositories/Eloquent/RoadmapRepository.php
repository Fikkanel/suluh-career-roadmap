<?php

namespace App\Repositories\Eloquent;

use App\Models\RoadmapArchive;
use App\Repositories\Contracts\RoadmapRepositoryInterface;

class RoadmapRepository implements RoadmapRepositoryInterface
{
    public function getCurrentRoadmap(int $userId): mixed
    {
        // TODO Slice 2: return active roadmap with stages
        return null;
    }

    public function archiveRoadmap(int $userId, string $reflection): mixed
    {
        return RoadmapArchive::create([
            'user_id'     => $userId,
            'reflection'  => $reflection,
            'snapshot'    => [],
            'archived_at' => now(),
        ]);
    }

    public function getArchives(int $userId): array
    {
        return RoadmapArchive::where('user_id', $userId)
            ->latest('archived_at')
            ->get()
            ->toArray();
    }
}
