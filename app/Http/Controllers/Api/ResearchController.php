<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProgress;
use App\Models\RoadmapArchive;
use App\Models\AssessmentResult;
use App\Models\Career;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

/**
 * API Publik untuk Peneliti Akademik (Fase 3).
 * 
 * Semua endpoint mengembalikan data agregat anonim.
 * Tidak ada PII (Personally Identifiable Information) yang dikembalikan.
 * 
 * Auth: Header X-Research-Key
 */
class ResearchController extends Controller
{
    private function getRequestInstitution(Request $request)
    {
        $key = $request->header('X-API-KEY');
        if (!$key) {
            return null;
        }

        return User::where('role', 'institution')
            ->whereNotNull('api_key')
            ->where('api_key', $key)
            ->first();
    }

    /**
     * GET /api/v1/research/summary
     * Ringkasan statistik platform secara keseluruhan.
     */
    public function summary(Request $request)
    {
        $institution = $this->getRequestInstitution($request);
        $institutionName = $institution ? $institution->name : null;
        $cacheKey = $institution ? 'research.summary.' . $institution->id : 'research.summary';

        $data = Cache::remember($cacheKey, 3600, function () use ($institutionName) {
            $usersQuery = User::where('is_admin', false);
            if ($institutionName) {
                $usersQuery->whereRaw('LOWER(university_name) = ?', [strtolower(trim($institutionName))]);
            }

            $totalUsers  = (clone $usersQuery)->count();
            $activeUsers = (clone $usersQuery)
                ->where('updated_at', '>=', now()->subDays(30))
                ->count();

            $usersWithCareer = (clone $usersQuery)->whereNotNull('current_career_id')->pluck('id');
            
            $done  = UserProgress::whereIn('user_id', $usersWithCareer)->where('status', 'done')->count();
            $total = UserProgress::whereIn('user_id', $usersWithCareer)->count();
            $avgCrs = $total > 0 ? round($done / $total * 100, 2) : 0;

            // Pivot rate
            $pivotQuery = RoadmapArchive::query();
            if ($institutionName) {
                $pivotQuery->whereIn('user_id', $usersWithCareer->merge((clone $usersQuery)->pluck('id')));
            }
            $pivotCount = $pivotQuery->count();
            $pivotRate  = $totalUsers > 0 ? round($pivotCount / $totalUsers * 100, 2) : 0;

            return [
                'total_users'           => $totalUsers,
                'active_users_30d'      => $activeUsers,
                'avg_career_readiness'  => $avgCrs,
                'pivot_rate_pct'        => $pivotRate,
                'users_with_career'     => $usersWithCareer->count(),
            ];
        });

        return response()->json([
            'success'    => true,
            'endpoint'   => 'research/summary',
            'note'       => 'Seluruh data merupakan agregat anonim. Tidak ada data personal yang dikembalikan.',
            'data'       => $data,
            'cached_at'  => now()->toIso8601String(),
        ]);
    }

    /**
     * GET /api/v1/research/career-distribution
     * Sebaran pemilihan karir oleh pengguna.
     */
    public function careerDistribution(Request $request)
    {
        $institution = $this->getRequestInstitution($request);
        $institutionName = $institution ? $institution->name : null;
        $cacheKey = $institution ? 'research.career_distribution.' . $institution->id : 'research.career_distribution';

        $data = Cache::remember($cacheKey, 3600, function () use ($institutionName) {
            $query = User::whereNotNull('current_career_id')
                ->join('careers', 'users.current_career_id', '=', 'careers.id');

            if ($institutionName) {
                $query->whereRaw('LOWER(users.university_name) = ?', [strtolower(trim($institutionName))]);
            }

            return $query->selectRaw('careers.name as career, careers.riasec_code, count(users.id) as total_users')
                ->groupBy('careers.id', 'careers.name', 'careers.riasec_code')
                ->orderByDesc('total_users')
                ->get()
                ->map(fn($r) => [
                    'career'        => $r->career,
                    'riasec_code'   => $r->riasec_code,
                    'total_users'   => $r->total_users,
                ]);
        });

        return response()->json([
            'success'   => true,
            'endpoint'  => 'research/career-distribution',
            'note'      => 'Distribusi pilihan karir. Tidak ada data pengguna individual yang disertakan.',
            'data'      => $data,
            'cached_at' => now()->toIso8601String(),
        ]);
    }

    /**
     * GET /api/v1/research/crs-trend
     * Tren Career Readiness Score rata-rata per bulan (6 bulan terakhir).
     */
    public function crsTrend(Request $request)
    {
        $institution = $this->getRequestInstitution($request);
        $institutionName = $institution ? $institution->name : null;
        $cacheKey = $institution ? 'research.crs_trend.' . $institution->id : 'research.crs_trend';

        $data = Cache::remember($cacheKey, 3600, function () use ($institutionName) {
            $trend = [];
            for ($i = 5; $i >= 0; $i--) {
                $monthKey = now()->subMonths($i)->format('Y-m');
                
                $usersQuery = User::whereNotNull('current_career_id')
                    ->whereRaw('DATE_FORMAT(updated_at, "%Y-%m") <= ?', [$monthKey]);

                if ($institutionName) {
                    $usersQuery->whereRaw('LOWER(university_name) = ?', [strtolower(trim($institutionName))]);
                }

                $userIds = $usersQuery->pluck('id');

                $done  = UserProgress::whereIn('user_id', $userIds)->where('status', 'done')->count();
                $total = UserProgress::whereIn('user_id', $userIds)->count();

                $trend[] = [
                    'month'          => $monthKey,
                    'avg_crs'        => $total > 0 ? round($done / $total * 100, 2) : 0,
                    'active_users'   => $userIds->count(),
                ];
            }
            return $trend;
        });

        return response()->json([
            'success'   => true,
            'endpoint'  => 'research/crs-trend',
            'note'      => 'Tren CRS rata-rata. Data agregat 6 bulan terakhir.',
            'data'      => $data,
            'cached_at' => now()->toIso8601String(),
        ]);
    }

    /**
     * GET /api/v1/research/pivot-analysis
     * Analisis pola perpindahan karir (Pivot).
     */
    public function pivotAnalysis(Request $request)
    {
        $institution = $this->getRequestInstitution($request);
        $institutionName = $institution ? $institution->name : null;
        $cacheKey = $institution ? 'research.pivot_analysis.' . $institution->id : 'research.pivot_analysis';

        $data = Cache::remember($cacheKey, 3600, function () use ($institutionName) {
            $usersQuery = User::where('is_admin', false);
            if ($institutionName) {
                $usersQuery->whereRaw('LOWER(university_name) = ?', [strtolower(trim($institutionName))]);
            }
            $totalUsers = (clone $usersQuery)->count();

            $pivotQuery = RoadmapArchive::query();
            if ($institutionName) {
                $pivotQuery->whereIn('user_id', (clone $usersQuery)->pluck('id'));
            }
            $totalPivots = $pivotQuery->count();
            $uniquePivoters = (clone $pivotQuery)->distinct('user_id')->count('user_id');

            // Distribusi jumlah pivot per user
            $distributionQuery = RoadmapArchive::query();
            if ($institutionName) {
                $distributionQuery->whereIn('user_id', (clone $usersQuery)->pluck('id'));
            }

            $pivotDistribution = $distributionQuery->selectRaw('user_id, count(*) as pivot_count')
                ->groupBy('user_id')
                ->get()
                ->groupBy('pivot_count')
                ->map->count();

            return [
                'total_pivots'          => $totalPivots,
                'unique_pivoters'       => $uniquePivoters,
                'pivot_rate_pct'        => $totalUsers > 0 ? round($uniquePivoters / $totalUsers * 100, 2) : 0,
                'pivot_distribution'    => $pivotDistribution,
            ];
        });

        return response()->json([
            'success'   => true,
            'endpoint'  => 'research/pivot-analysis',
            'note'      => 'Analisis perpindahan karir (Pivot). Tidak ada data identitas pengguna yang disertakan.',
            'data'      => $data,
            'cached_at' => now()->toIso8601String(),
        ]);
    }
}
