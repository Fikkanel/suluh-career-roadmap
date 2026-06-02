<?php

namespace App\Services;

use App\Models\User;
use App\Models\ImpactSurvey;
use App\Models\AssessmentResult;
use Illuminate\Support\Facades\Cache;

class ImpactCalculatorService
{
    /**
     * Returns public aggregate stats, cached for 24 hours.
     * Only anonymous aggregates — never individual data.
     */
    public function getPublicStats(): array
    {
        return Cache::remember('suluh.impact.public_stats', 3600, function () {
            $totalUsers  = User::where('is_admin', false)->count();
            $activeUsers = User::where('is_admin', false)
                ->where('updated_at', '>=', now()->subDays(30))
                ->count();

            $pivotCount = \App\Models\RoadmapArchive::count();
            $pivotRate  = $totalUsers > 0
                ? (int) round($pivotCount / $totalUsers * 100)
                : 0;

            $usersWithCareer = User::whereNotNull('current_career_id')->get();
            $crsValues = $usersWithCareer->map(function ($user) {
                $total = \App\Models\UserProgress::where('user_id', $user->id)->count();
                $done  = \App\Models\UserProgress::where('user_id', $user->id)->where('status', 'done')->count();
                return $total > 0 ? round($done / $total * 100) : 0;
            });
            $avgCrs = $crsValues->isNotEmpty() ? (int) round($crsValues->avg()) : 0;

            $surveyTotal    = ImpactSurvey::count();
            $surveyPositive = ImpactSurvey::whereNotNull('crs_after')
                ->whereNotNull('crs_before')
                ->whereRaw('crs_after > crs_before')
                ->count();
            $positivePct    = $surveyTotal > 0
                ? (int) round($surveyPositive / $surveyTotal * 100)
                : 0;

            // Province distribution (anonymized aggregate only)
            $provinceDistribution = User::where('is_admin', false)
                ->whereNotNull('province')
                ->selectRaw('province, count(*) as total')
                ->groupBy('province')
                ->pluck('total', 'province')
                ->toArray();

            return [
                'active_users'           => $activeUsers,
                'total_users'            => $totalUsers,
                'avg_crs'                => $avgCrs,
                'positive_change_pct'    => $positivePct,
                'pivot_rate'             => $pivotRate,
                'province_distribution'  => $provinceDistribution,
                'cached_at'              => now()->toDateTimeString(),
            ];
        });
    }

    /**
     * Force-refresh the public stats cache.
     */
    public function refreshCache(): void
    {
        Cache::forget('suluh.impact.public_stats');
        $this->getPublicStats();
    }
}
