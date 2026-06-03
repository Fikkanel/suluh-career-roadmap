<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProgress;
use App\Models\RoadmapArchive;
use App\Models\AssessmentResult;
use Illuminate\Support\Facades\Cache;

/**
 * Dashboard untuk Institusi Mitra (Kampus/Lembaga Pelatihan).
 *
 * PRINSIP PRIVASI (sesuai PRD Konstitusi P2):
 * Tidak ada data individual yang ditampilkan. Semua output adalah agregat anonim.
 */
class InstitutionDashboardController extends Controller
{
    public function index()
    {
        $stats = Cache::remember('institution.dashboard.stats', 1800, function () {
            // Hanya hitung pengguna dengan role 'user' (bukan admin/mentor/institution)
            $allUsers = User::where('role', 'user')->orWhere(function($q) {
                $q->where('is_admin', false)->whereNull('role');
            });

            $totalUsers  = (clone $allUsers)->count();
            $activeUsers = (clone $allUsers)->where('updated_at', '>=', now()->subDays(30))->count();
            $usersWithCareer = (clone $allUsers)->whereNotNull('current_career_id')->pluck('id');

            // Rata-rata CRS agregat
            $done  = UserProgress::whereIn('user_id', $usersWithCareer)->where('status', 'done')->count();
            $total = UserProgress::whereIn('user_id', $usersWithCareer)->count();
            $avgCrs = $total > 0 ? round($done / $total * 100, 1) : 0;

            // Pivot rate
            $pivotCount = RoadmapArchive::whereIn('user_id', $usersWithCareer->merge(
                (clone $allUsers)->pluck('id')
            ))->count();
            $pivotRate = $totalUsers > 0 ? round($pivotCount / $totalUsers * 100, 1) : 0;

            // Sebaran karir (agregat)
            $careerDistribution = User::where(function($q) {
                    $q->where('role', 'user')->orWhere(function($q2) {
                        $q2->where('is_admin', false)->whereNull('role');
                    });
                })
                ->whereNotNull('current_career_id')
                ->join('careers', 'users.current_career_id', '=', 'careers.id')
                ->selectRaw('careers.name as career_name, careers.riasec_code, count(users.id) as total')
                ->groupBy('careers.id', 'careers.name', 'careers.riasec_code')
                ->orderByDesc('total')
                ->get();

            // Distribusi progres (berapa % pengguna di tiap bucket CRS)
            $progressBuckets = ['0-25%' => 0, '26-50%' => 0, '51-75%' => 0, '76-100%' => 0];
            foreach ($usersWithCareer as $uid) {
                $t = UserProgress::where('user_id', $uid)->count();
                $d = UserProgress::where('user_id', $uid)->where('status', 'done')->count();
                $pct = $t > 0 ? round($d / $t * 100) : 0;
                if ($pct <= 25)      $progressBuckets['0-25%']++;
                elseif ($pct <= 50)  $progressBuckets['26-50%']++;
                elseif ($pct <= 75)  $progressBuckets['51-75%']++;
                else                 $progressBuckets['76-100%']++;
            }

            // Tren pengguna baru per bulan
            $monthlyGrowth = User::where('is_admin', false)
                ->where('created_at', '>=', now()->subMonths(6))
                ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, count(*) as total')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            return compact(
                'totalUsers', 'activeUsers', 'avgCrs',
                'pivotRate', 'careerDistribution',
                'progressBuckets', 'monthlyGrowth'
            );
        });

        return view('institution.dashboard', $stats);
    }

    public function generateApiKey()
    {
        $user = auth()->user();
        
        // Generate secure unique API key
        $user->api_key = 'slh_inst_' . bin2hex(random_bytes(16));
        $user->save();

        return redirect()->route('institution.dashboard')->with('success', 'API Key berhasil dibuat!');
    }

    public function revokeApiKey()
    {
        $user = auth()->user();
        $user->api_key = null;
        $user->save();

        return redirect()->route('institution.dashboard')->with('success', 'API Key berhasil dinonaktifkan.');
    }
}
