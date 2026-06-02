<?php

namespace App\Policies;

use App\Models\RoadmapArchive;
use App\Models\User;

class RoadmapPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, RoadmapArchive $roadmapArchive): bool
    {
        return $user->id === $roadmapArchive->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, RoadmapArchive $roadmapArchive): bool
    {
        return $user->id === $roadmapArchive->user_id;
    }

    public function delete(User $user, RoadmapArchive $roadmapArchive): bool
    {
        return $user->id === $roadmapArchive->user_id;
    }

    public function restore(User $user, RoadmapArchive $roadmapArchive): bool
    {
        return $user->id === $roadmapArchive->user_id;
    }

    public function forceDelete(User $user, RoadmapArchive $roadmapArchive): bool
    {
        return $user->id === $roadmapArchive->user_id;
    }
}
