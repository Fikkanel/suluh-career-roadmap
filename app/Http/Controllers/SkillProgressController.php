<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Eloquent\ProgressRepository;
use App\Services\ScoringService;

class SkillProgressController extends Controller
{
    public function __construct(
        private readonly ProgressRepository $progressRepo,
        private readonly ScoringService     $scoring,
    ) {}

    public function index()
    {
        $user   = auth()->user();
        $career = $user->currentCareer;

        if (! $career) {
            return redirect()->route('assessment');
        }

        $progressRows = $this->progressRepo->getByUser($user->id);

        // Fallback: If no progress records exist but user has a career, create them
        if (empty($progressRows) && $career) {
            \Log::info("Creating missing UserProgress records for user", [
                'user_id' => $user->id,
                'career_id' => $career->id
            ]);

            // Get all skills for this career
            $skills = \App\Models\Skill::where('career_id', $career->id)->get();
            
            foreach ($skills as $skill) {
                \App\Models\UserProgress::firstOrCreate(
                    ['user_id' => $user->id, 'skill_id' => $skill->id],
                    ['status' => 'not_started']
                );
            }

            // Reload progress after creating records
            $progressRows = $this->progressRepo->getByUser($user->id);
        }

        $skills = collect($progressRows)->map(fn ($p) => [
            'id'          => $p['skill_id'],
            'name'        => $p['skill']['name'] ?? '—',
            'level'       => $p['skill']['level'] ?? 'beginner',
            'status'      => $p['status'],
            'transferable'=> (bool) ($p['skill']['is_transferable'] ?? false),
        ])->sortBy(fn ($s) => ['beginner' => 0, 'intermediate' => 1, 'advanced' => 2][$s['level']] ?? 0)
          ->values()
          ->toArray();

        $crs = $this->progressRepo->calculateCrs($user->id, $career->id);

        return view('app.skill-progress', compact('skills', 'crs'));
    }

    public function update(Request $request, $skillId, \App\Services\ContextScoreService $contextService)
    {
        $request->validate(['status' => 'required|in:not_started,learning,in_progress,done']);

        $user = auth()->user();
        $status = $request->input('status');

        $updated = $this->progressRepo->updateStatus(
            $user->id,
            (int) $skillId,
            $status
        );

        if ($updated) {
            // Recalculate CRS
            $crs = $this->progressRepo->calculateCrs($user->id, $user->current_career_id);
            
            // Get Context Score to determine notification style
            $contextScore = \App\Models\ContextScore::where('user_id', $user->id)->first();
            $contextLevel = $contextScore ? $contextScore->level : 'medium';
            
            // Trigger dynamic behavior signals if done
            if ($status === 'done') {
                if ($contextScore) {
                    $factors = $contextScore->factors ?? [];
                    $factors['behavior_signals']['skills_completed'] = ($factors['behavior_signals']['skills_completed'] ?? 0) + 1;
                    $contextScore->factors = $factors;
                    $contextScore->save();
                }

                // Send Contextual Notification
                $messageType = 'encouragement';
                if (in_array($crs, [25, 50, 75, 100])) {
                    $messageType = 'milestone';
                }

                // We don't want to spam notifications for every single % increase, 
                // but since it's an MVP, let's notify when a skill is done.
                $user->notify(new \App\Notifications\ProgressContextNotification($crs, $contextLevel, $messageType));
            }

            return back()->with('success', 'Status skill diperbarui.');
        }
        return back()->withErrors(['Gagal memperbarui status.']);
    }
}
