<?php

namespace App\Http\Controllers;

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

    public function show()
    {
        $user   = auth()->user();
        $career = $user->currentCareer;

        $transferableSkills = [];
        if ($career) {
            $transferableSkills = collect($this->progressRepo->getByUser($user->id))
                ->where('status', 'done')
                ->map(fn ($p) => [
                    'name'  => $p['skill']['name'] ?? '—',
                    'level' => $p['skill']['level'] ?? 'beginner',
                ])
                ->values()
                ->toArray();
        }

        return view('app.pivot', compact('transferableSkills'));
    }

    public function store(Request $request)
    {
        $request->validate(['reflection' => 'nullable|string|max:2000']);

        $user   = auth()->user();
        $career = $user->currentCareer;

        if ($career) {
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

            // Hapus cache narasi LLM agar kuesioner dan rekomendasi berikutnya segar
            \App\Models\LlmNarrativeCache::where('user_id', $user->id)->delete();

            $this->llm->generate($user->id, 'pivot_transfer', [
                'from_career'       => $career->name,
                'completed_skills'  => $completedSkills,
                'total_skills'      => $totalSkills,
            ]);
        }

        return redirect()->route('assessment')->with('pivot', true);
    }
}
