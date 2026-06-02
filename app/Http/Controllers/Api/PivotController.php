<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RoadmapArchive;
use App\Models\UserProgress;
use App\Services\LLMNarrativeService;
use App\Repositories\Eloquent\ProgressRepository;

class PivotController extends Controller
{
    public function __construct(
        private readonly ProgressRepository $progressRepo,
        private readonly LLMNarrativeService $llm,
    ) {}

    public function store(Request $request)
    {
        $request->validate(['reflection' => 'nullable|string|max:2000']);

        $user   = $request->user();
        $career = $user->currentCareer;

        if (! $career) {
            return response()->json(['message' => 'Tidak ada karir aktif untuk di-pivot.'], 400);
        }

        $allProgress     = collect($this->progressRepo->getByUser($user->id));
        $completedSkills = $allProgress->where('status', 'done')->count();
        $totalSkills     = $allProgress->count();

        $snapshot = $allProgress->map(fn ($p) => [
            'skill_id' => $p['skill_id'],
            'name'     => $p['skill']['name'] ?? '—',
            'status'   => $p['status'],
        ])->toArray();

        RoadmapArchive::create([
            'user_id'          => $user->id,
            'career_id'        => $career->id,
            'career_name'      => $career->name,
            'reflection'       => $request->input('reflection'),
            'completed_skills' => $completedSkills,
            'total_skills'     => $totalSkills,
            'snapshot'         => $snapshot,
            'archived_at'      => now(),
        ]);

        UserProgress::where('user_id', $user->id)->delete();
        $user->update(['current_career_id' => null]);

        $this->llm->generate($user->id, 'pivot_transfer', [
            'from_career'      => $career->name,
            'completed_skills' => $completedSkills,
            'total_skills'     => $totalSkills,
        ]);

        return response()->json([
            'message' => 'Pivot berhasil. Roadmap lama diarsipkan.',
            'archived' => [
                'career'           => $career->name,
                'completed_skills' => $completedSkills,
                'total_skills'     => $totalSkills,
            ],
        ]);
    }
}
