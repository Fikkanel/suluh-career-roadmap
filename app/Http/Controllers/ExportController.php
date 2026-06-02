<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RoadmapArchive;
use App\Models\UserProgress;
use App\Models\AssessmentResult;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportController extends Controller
{
    public function index()
    {
        $includedData = [
            'Profil pengguna',
            'Hasil asesmen RIASEC & Big Five',
            'Roadmap aktif & progress skill',
            'Riwayat pivot & arsip perjalanan',
            'Hasil survei dampak',
        ];
        return view('app.export', compact('includedData'));
    }

    public function pdf(Request $request)
    {
        $user    = auth()->user();
        $payload = $this->buildExportPayload($user);

        $pdf = Pdf::loadView('exports.pdf', ['user' => $user, 'data' => $payload])
            ->setPaper('a4', 'portrait');

        return $pdf->download('suluh-export-' . now()->format('Ymd') . '.pdf');
    }

    public function json(Request $request)
    {
        $user    = auth()->user();
        $payload = $this->buildExportPayload($user);

        $json    = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $filename = 'suluh-export-' . now()->format('Ymd') . '.json';

        return response()->streamDownload(
            fn () => print($json),
            $filename,
            ['Content-Type' => 'application/json']
        );
    }

    private function buildExportPayload($user): array
    {
        $career   = $user->currentCareer;
        $progress = UserProgress::where('user_id', $user->id)->with('skill')->get();
        $archives = RoadmapArchive::where('user_id', $user->id)->with('career')->latest('archived_at')->get();
        $results  = AssessmentResult::where('user_id', $user->id)->latest()->get();

        return [
            'exported_at' => now()->toIso8601String(),
            'profile' => [
                'name'             => $user->name,
                'email'            => $user->email,
                'age_range'        => $user->age_range,
                'education_level'  => $user->education_level,
                'work_experience'  => $user->work_experience,
                'current_career'   => $career?->name,
                'created_at'       => $user->created_at?->toIso8601String(),
            ],
            'assessment_results' => $results->map(fn ($r) => [
                'date'        => $r->created_at?->toIso8601String(),
                'riasec'      => $r->riasec_scores,
                'big_five'    => $r->big_five_scores,
                'top_careers' => $r->top_career_ids,
            ])->toArray(),
            'current_progress' => $progress->map(fn ($p) => [
                'skill'        => $p->skill?->name,
                'level'        => $p->skill?->level,
                'status'       => $p->status,
                'started_at'   => $p->started_at?->toIso8601String(),
                'completed_at' => $p->completed_at?->toIso8601String(),
            ])->toArray(),
            'roadmap_archives' => $archives->map(fn ($a) => [
                'career'           => $a->career_name,
                'archived_at'      => $a->archived_at?->toIso8601String(),
                'completed_skills' => $a->completed_skills,
                'total_skills'     => $a->total_skills,
                'reflection'       => $a->reflection,
                'snapshot'         => $a->snapshot,
            ])->toArray(),
        ];
    }
}
