<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AssessmentResult;
use App\Models\RoadmapArchive;
use App\Models\UserProgress;
use App\Models\Career;
use Illuminate\Support\Facades\Cache;

class AdminDashboardController extends Controller
{
    public function index()
    {
        try {
            $stats = Cache::remember('admin_dashboard_stats', 3600, function () {
                $totalUsers       = User::where('is_admin', false)->count();
                $activeThisMonth  = User::where('is_admin', false)
                    ->where('updated_at', '>=', now()->startOfMonth())
                    ->count();
                $assessmentsDone  = AssessmentResult::count();
                $pivotCount       = RoadmapArchive::count();

                $avgCrs = 0;
                if ($totalUsers > 0) {
                    $usersWithCareer = User::whereNotNull('current_career_id')->get();
                    if ($usersWithCareer->isNotEmpty()) {
                        $crsValues = $usersWithCareer->map(function ($user) {
                            $total = UserProgress::where('user_id', $user->id)->count();
                            $done  = UserProgress::where('user_id', $user->id)->where('status', 'done')->count();
                            return $total > 0 ? round($done / $total * 100) : 0;
                        });
                        $avgCrs = $crsValues->isNotEmpty() ? (int) round($crsValues->avg()) : 0;
                    }
                }

                // Fix: Add better null handling for career distribution
                $careersDistribution = [];
                $usersWithCareers = User::whereNotNull('current_career_id')
                    ->with('currentCareer')
                    ->get();
                
                if ($usersWithCareers->isNotEmpty()) {
                    $careersDistribution = $usersWithCareers
                        ->filter(fn ($u) => $u->currentCareer !== null)
                        ->groupBy(fn ($u) => $u->currentCareer->name)
                        ->map->count()
                        ->sortDesc()
                        ->toArray();
                }

                if (empty($careersDistribution)) {
                    $careersDistribution = ['(Belum ada data)' => 0];
                }

                return [
                    'total_users'          => $totalUsers,
                    'active_this_month'    => $activeThisMonth,
                    'avg_crs'              => $avgCrs,
                    'assessments_done'     => $assessmentsDone,
                    'pivot_count'          => $pivotCount,
                    'careers_distribution' => $careersDistribution,
                ];
            });

            return view('admin.dashboard', compact('stats'));
        } catch (\Exception $e) {
            \Log::error('Admin Dashboard Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return view with empty stats on error
            $stats = [
                'total_users'          => 0,
                'active_this_month'    => 0,
                'avg_crs'              => 0,
                'assessments_done'     => 0,
                'pivot_count'          => 0,
                'careers_distribution' => ['(Error loading data)' => 0],
            ];
            
            return view('admin.dashboard', compact('stats'))
                ->with('error', 'Terjadi kesalahan saat memuat data dashboard. Silakan coba lagi.');
        }
    }
}
