<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Career;
use App\Events\CareerChosen;
use App\Services\RoadmapGeneratorService;

class CareerController extends Controller
{
    public function __construct(
        private readonly RoadmapGeneratorService $roadmapGenerator,
    ) {}

    public function show($id)
    {
        $career = Career::with('skills')->where('is_active', true)->findOrFail($id);

        $careerData = [
            'id'               => $career->id,
            'name'             => $career->name,
            'description'      => $career->description,
            'riasec_code'      => $career->riasec_code,
            'industry_standard'=> $career->industry_standard,
            'skills'           => $career->skills->map(fn ($s) => ['name' => $s->name, 'level' => $s->level])->toArray(),
        ];

        return view('app.career-detail', ['career' => $careerData]);
    }

    public function choose(Request $request, $id)
    {
        try {
            $career = Career::where('is_active', true)->findOrFail($id);
            $user   = auth()->user();

            // Update user's current career
            $user->update(['current_career_id' => $career->id]);

            // Generate roadmap stages
            $stages = $this->roadmapGenerator->generate($user->id, $career->id);

            // Create UserProgress records for each skill
            $createdCount = 0;
            foreach ($stages as $stage) {
                $progress = \App\Models\UserProgress::firstOrCreate(
                    ['user_id' => $user->id, 'skill_id' => $stage['skill_id']],
                    ['status'  => $stage['status']]
                );
                if ($progress->wasRecentlyCreated) {
                    $createdCount++;
                }
            }

            \Log::info("Career selected", [
                'user_id' => $user->id,
                'career_id' => $career->id,
                'career_name' => $career->name,
                'stages_count' => count($stages),
                'progress_created' => $createdCount
            ]);

            // Dispatch event for dynamic context score
            CareerChosen::dispatch($user, $career);

            return redirect()->route('roadmap')
                ->with('success', "Karir {$career->name} berhasil dipilih! Roadmap kamu sudah siap.");
        } catch (\Exception $e) {
            \Log::error("Career selection failed", [
                'user_id' => auth()->id(),
                'career_id' => $id,
                'error' => $e->getMessage()
            ]);

            return back()->withErrors(['error' => 'Terjadi kesalahan saat memilih karir. Silakan coba lagi.']);
        }
    }
}
