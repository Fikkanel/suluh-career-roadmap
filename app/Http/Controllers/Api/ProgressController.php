<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Eloquent\ProgressRepository;

class ProgressController extends Controller
{
    public function __construct(
        private readonly ProgressRepository $progressRepo,
    ) {}

    public function update(Request $request)
    {
        $data = $request->validate([
            'skill_id' => 'required|integer|exists:skills,id',
            'status'   => 'required|in:not_started,learning,in_progress,done',
        ]);

        $updated = $this->progressRepo->updateStatus(
            $request->user()->id,
            $data['skill_id'],
            $data['status']
        );

        if ($updated) {
            return response()->json(['message' => 'Status skill diperbarui.']);
        }

        return response()->json(['message' => 'Gagal memperbarui status.'], 400);
    }

    public function summary()
    {
        $user   = auth()->user();
        $career = $user->currentCareer;

        if (! $career) {
            return response()->json(['message' => 'Belum memilih karir.'], 404);
        }

        $progressRows = $this->progressRepo->getByUser($user->id);
        $crs          = $this->progressRepo->calculateCrs($user->id, $career->id);

        $skills = collect($progressRows)->map(fn ($p) => [
            'skill_id' => $p['skill_id'],
            'name'     => $p['skill']['name'] ?? '—',
            'level'    => $p['skill']['level'] ?? 'beginner',
            'status'   => $p['status'],
        ])->values()->toArray();

        return response()->json([
            'career_readiness_score' => $crs,
            'total_skills'           => count($skills),
            'completed'              => collect($skills)->where('status', 'done')->count(),
            'in_progress'            => collect($skills)->where('status', 'in_progress')->count(),
            'not_started'            => collect($skills)->where('status', 'not_started')->count(),
            'skills'                 => $skills,
        ]);
    }
}
