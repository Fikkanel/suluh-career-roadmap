<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ImpactCalculatorService;
use App\Models\User;
use App\Models\UserProgress;
use App\Models\RoadmapArchive;
use App\Models\Career;
use Illuminate\Support\Facades\DB;

class PublicController extends Controller
{
    public function __construct(
        private readonly ImpactCalculatorService $impact,
    ) {}

    public function landing()
    {
        return view('public.landing');
    }

    public function impact()
    {
        $raw = $this->impact->getPublicStats();
        $updatedAt = $raw['cached_at'] ?? now()->toDateTimeString();

        $stats = [
            [
                'label'       => 'Pengguna Aktif (30 hari)',
                'value'       => number_format($raw['active_users']),
                'explanation' => 'Pengguna yang aktif menggunakan platform dalam 30 hari terakhir. Data agregat anonim.',
                'variant'     => 'positive',
                'updatedAt'   => $updatedAt,
            ],
            [
                'label'       => 'Rata-rata CRS',
                'value'       => $raw['avg_crs'] . '%',
                'explanation' => 'Rata-rata Career Readiness Score pengguna yang sedang aktif menjalani roadmap.',
                'variant'     => 'positive',
                'updatedAt'   => $updatedAt,
            ],
            [
                'label'       => 'Melaporkan Perubahan Positif',
                'value'       => $raw['positive_change_pct'] . '%',
                'explanation' => 'Persentase pengguna yang melaporkan dampak positif dalam survei longitudinal.',
                'variant'     => $raw['positive_change_pct'] >= 50 ? 'positive' : 'monitored',
                'updatedAt'   => $updatedAt,
            ],
            [
                'label'       => 'Pivot Rate',
                'value'       => $raw['pivot_rate'] . '%',
                'explanation' => 'Persentase pengguna yang melakukan pivot karir. Dimonitor sebagai data, bukan masalah.',
                'variant'     => 'monitored',
                'updatedAt'   => $updatedAt,
            ],
        ];

        $provinceDistribution = $raw['province_distribution'] ?? [];

        // --- Chart Data untuk Grafik Lanjutan (Fase 3) ---

        // Donut chart: Sebaran pemilihan karir
        $careerDistribution = User::whereNotNull('current_career_id')
            ->join('careers', 'users.current_career_id', '=', 'careers.id')
            ->selectRaw('careers.name as career_name, count(users.id) as total')
            ->groupBy('careers.id', 'careers.name')
            ->orderByDesc('total')
            ->limit(6)
            ->get()
            ->map(fn($r) => ['label' => $r->career_name, 'value' => $r->total]);

        $driver = DB::connection()->getDriverName();
        $createdFormat = $driver === 'sqlite' ? "strftime('%Y-%m', created_at)" : "DATE_FORMAT(created_at, '%Y-%m')";
        $updatedFormat = $driver === 'sqlite' ? "strftime('%Y-%m', updated_at)" : "DATE_FORMAT(updated_at, '%Y-%m')";

        // Bar chart: Tren pengguna baru per bulan (6 bulan terakhir)
        $monthlyGrowth = User::where('is_admin', false)
            ->where('created_at', '>=', now()->subMonths(6))
            ->selectRaw("{$createdFormat} as month, count(*) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        // Line chart: Rata-rata CRS per bulan
        $avgCrsByMonth = collect();
        for ($i = 5; $i >= 0; $i--) {
            $monthKey = now()->subMonths($i)->format('Y-m');
            $usersThisMonth = User::whereNotNull('current_career_id')
                ->whereRaw("{$updatedFormat} <= ?", [$monthKey])
                ->pluck('id');
            if ($usersThisMonth->isNotEmpty()) {
                $done  = UserProgress::whereIn('user_id', $usersThisMonth)->where('status', 'done')->count();
                $total = UserProgress::whereIn('user_id', $usersThisMonth)->count();
                $avgCrsByMonth[$monthKey] = $total > 0 ? round($done / $total * 100) : 0;
            } else {
                $avgCrsByMonth[$monthKey] = 0;
            }
        }

        return view('public.impact', compact(
            'stats',
            'provinceDistribution',
            'careerDistribution',
            'monthlyGrowth',
            'avgCrsByMonth'
        ));
    }

    public function sunsetPolicy()
    {
        return view('public.sunset-policy');
    }
}
