<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\AssessmentResult;
use App\Models\RoadmapArchive;
use App\Models\UserProgress;
use App\Models\User;
use App\Policies\AssessmentPolicy;
use App\Policies\RoadmapPolicy;
use App\Policies\ProgressPolicy;
use App\Policies\AdminPolicy;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\AssessmentRepositoryInterface;
use App\Repositories\Contracts\RoadmapRepositoryInterface;
use App\Repositories\Contracts\ProgressRepositoryInterface;
use App\Repositories\Contracts\ImpactRepositoryInterface;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Eloquent\AssessmentRepository;
use App\Repositories\Eloquent\RoadmapRepository;
use App\Repositories\Eloquent\ProgressRepository;
use App\Repositories\Eloquent\ImpactRepository;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class,       UserRepository::class);
        $this->app->bind(AssessmentRepositoryInterface::class, AssessmentRepository::class);
        $this->app->bind(RoadmapRepositoryInterface::class,    RoadmapRepository::class);
        $this->app->bind(ProgressRepositoryInterface::class,   ProgressRepository::class);
        $this->app->bind(ImpactRepositoryInterface::class,     ImpactRepository::class);
    }

    public function boot(): void
    {
        Gate::define('view-public-impact', function ($user = null) {
            return true;
        });

        Gate::define('manage-mentee', function ($user, $mentee) {
            // TODO Slice 2: check mentor assignment table
            return false;
        });

        Gate::policy(AssessmentResult::class, AssessmentPolicy::class);
        Gate::policy(RoadmapArchive::class, RoadmapPolicy::class);
        Gate::policy(UserProgress::class, ProgressPolicy::class);
        Gate::policy(User::class, AdminPolicy::class);
    }
}
