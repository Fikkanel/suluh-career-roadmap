<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\ImpactSurvey;
use App\Models\AssessmentResult;
use App\Models\UserProgress;
use Illuminate\Console\Command;

class SendImpactSurveys extends Command
{
    protected $signature = 'surveys:send';
    protected $description = 'Create impact survey records for users who have passed 3-month or 6-month milestones';

    public function handle(): int
    {
        $users = User::where('is_admin', false)
            ->whereNotNull('current_career_id')
            ->get();

        $created3m = 0;
        $created6m = 0;

        foreach ($users as $user) {
            $createdAt = $user->created_at;

            // Check 3-month milestone
            if ($createdAt->copy()->addMonths(3)->isPast()) {
                $existing3m = ImpactSurvey::where('user_id', $user->id)
                    ->where('type', '3_months')
                    ->exists();

                if (!$existing3m) {
                    $crsBefore = $this->calculateCrs($user->id);

                    ImpactSurvey::create([
                        'user_id'    => $user->id,
                        'type'       => '3_months',
                        'crs_before' => $crsBefore,
                        'answers'    => null,
                    ]);
                    $created3m++;
                }
            }

            // Check 6-month milestone
            if ($createdAt->copy()->addMonths(6)->isPast()) {
                $existing6m = ImpactSurvey::where('user_id', $user->id)
                    ->where('type', '6_months')
                    ->exists();

                if (!$existing6m) {
                    $crsBefore = $this->calculateCrs($user->id);

                    ImpactSurvey::create([
                        'user_id'    => $user->id,
                        'type'       => '6_months',
                        'crs_before' => $crsBefore,
                        'answers'    => null,
                    ]);
                    $created6m++;
                }
            }
        }

        $this->info("Created {$created3m} 3-month surveys and {$created6m} 6-month surveys.");

        return Command::SUCCESS;
    }

    private function calculateCrs(int $userId): int
    {
        $user = User::find($userId);
        if (!$user || !$user->current_career_id) return 0;

        $total = UserProgress::where('user_id', $userId)->count();
        if ($total === 0) return 0;

        $done = UserProgress::where('user_id', $userId)->where('status', 'done')->count();

        return (int) round($done / $total * 100);
    }
}
