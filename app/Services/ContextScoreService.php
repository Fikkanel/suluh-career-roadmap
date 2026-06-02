<?php

namespace App\Services;

use App\Models\ContextScore;

class ContextScoreService
{
    /**
     * Calculate context score (0–100) from onboarding data + readiness signals.
     * Behavior signals (from events) are incorporated when present.
     */
    public function calculateFromOnboarding(array $onboardingData): array
    {
        $score = 30;
        $level = 'low';
        $factors = [];

        // Work experience signal
        $workExp = $onboardingData['work_experience'] ?? '';
        if ($workExp !== '' && $workExp !== 'none') {
            $score += 25;
            $factors['work_experience'] = $workExp;
        }

        // Education level signal
        $eduLevel = $onboardingData['education_level'] ?? '';
        if ($eduLevel !== '' && in_array($eduLevel, ['s1_done'])) {
            $score += 15;
            $factors['education_level'] = $eduLevel;
        }

        // Exploration readiness signal (Constitution P3: eksplorasi = kemajuan)
        $readiness = $onboardingData['exploration_readiness'] ?? '';
        $readinessScores = [
            'very_comfortable' => 20,
            'comfortable'      => 15,
            'cautious'         => 10,
            'prefer_known'     => 5,
        ];
        if ($readiness !== '' && isset($readinessScores[$readiness])) {
            $score += $readinessScores[$readiness];
            $factors['exploration_readiness'] = $readiness;
        }

        // Support level signal
        $support = $onboardingData['support_level'] ?? '';
        $supportScores = [
            'strong'   => 10,
            'moderate' => 7,
            'limited'  => 4,
            'none'     => 2,
        ];
        if ($support !== '' && isset($supportScores[$support])) {
            $score += $supportScores[$support];
            $factors['support_level'] = $support;
        }

        // Behavior signals (from events: login frequency, skill progress, career choices)
        $behavior = $onboardingData['behavior_signals'] ?? [];
        if (!empty($behavior)) {
            $loginCount = $behavior['login_count'] ?? 0;
            if ($loginCount >= 5) $score += 3;
            elseif ($loginCount >= 2) $score += 1;

            $skillsCompleted = $behavior['skills_completed'] ?? 0;
            if ($skillsCompleted >= 3) $score += 5;
            elseif ($skillsCompleted >= 1) $score += 2;

            $careerChosenCount = $behavior['career_chosen_count'] ?? 0;
            if ($careerChosenCount >= 2) $score += 2;

            $factors['behavior_signals'] = $behavior;
        }

        if ($score >= 70) $level = 'high';
        elseif ($score >= 40) $level = 'medium';

        return ['score' => min(100, $score), 'level' => $level, 'factors' => $factors];
    }

    /**
     * Update context score for a user, separating score/level from factors.
     */
    public function updateForUser(int $userId, array $result): void
    {
        $factors = $result['factors'] ?? [];

        // Merge any previous behavior signals to preserve history
        $existing = ContextScore::where('user_id', $userId)->first();
        $existingFactors = $existing?->factors ?? [];
        if (isset($existingFactors['behavior_signals'])) {
            $factors['behavior_signals'] = array_merge(
                $existingFactors['behavior_signals'],
                $factors['behavior_signals'] ?? []
            );
        }

        ContextScore::updateOrCreate(
            ['user_id' => $userId],
            [
                'score'   => $result['score'],
                'level'   => $result['level'],
                'factors' => $factors,
            ]
        );
    }
}
