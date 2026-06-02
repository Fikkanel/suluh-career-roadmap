<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Models\ContextScore;
use App\Services\ContextScoreService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContextScoreServiceTest extends TestCase
{
    use RefreshDatabase;

    private ContextScoreService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ContextScoreService();
    }

    public function test_baseline_score_with_no_data(): void
    {
        $result = $this->service->calculateFromOnboarding([]);
        $this->assertEquals(30, $result['score']);
        $this->assertEquals('low', $result['level']);
    }

    public function test_work_experience_increases_score(): void
    {
        $none = $this->service->calculateFromOnboarding(['work_experience' => 'none']);
        $withExp = $this->service->calculateFromOnboarding(['work_experience' => '1-2y']);

        $this->assertEquals(30, $none['score']);
        $this->assertEquals(55, $withExp['score']);
    }

    public function test_education_level_s1_done_increases_score(): void
    {
        $noEdu = $this->service->calculateFromOnboarding([]);
        $withEdu = $this->service->calculateFromOnboarding(['education_level' => 's1_done']);

        $this->assertEquals(30, $noEdu['score']);
        $this->assertEquals(45, $withEdu['score']);
    }

    public function test_exploration_readiness_very_comfortable(): void
    {
        $result = $this->service->calculateFromOnboarding(['exploration_readiness' => 'very_comfortable']);
        $this->assertEquals(50, $result['score']);
    }

    public function test_exploration_readiness_prefer_known(): void
    {
        $result = $this->service->calculateFromOnboarding(['exploration_readiness' => 'prefer_known']);
        $this->assertEquals(35, $result['score']);
    }

    public function test_support_level_strong(): void
    {
        $result = $this->service->calculateFromOnboarding(['support_level' => 'strong']);
        $this->assertEquals(40, $result['score']);
    }

    public function test_support_level_none(): void
    {
        $result = $this->service->calculateFromOnboarding(['support_level' => 'none']);
        $this->assertEquals(32, $result['score']);
    }

    public function test_level_high_when_score_70_or_above(): void
    {
        $result = $this->service->calculateFromOnboarding([
            'work_experience'       => '3+y',
            'education_level'       => 's1_done',
            'exploration_readiness' => 'very_comfortable',
            'support_level'         => 'strong',
        ]);

        $this->assertGreaterThanOrEqual(70, $result['score']);
        $this->assertEquals('high', $result['level']);
    }

    public function test_level_medium_when_score_40_to_69(): void
    {
        $result = $this->service->calculateFromOnboarding([
            'work_experience' => '1-2y',
        ]);

        $this->assertGreaterThanOrEqual(40, $result['score']);
        $this->assertLessThan(70, $result['score']);
        $this->assertEquals('medium', $result['level']);
    }

    public function test_level_low_when_score_below_40(): void
    {
        $result = $this->service->calculateFromOnboarding([]);
        $this->assertLessThan(40, $result['score']);
        $this->assertEquals('low', $result['level']);
    }

    public function test_score_capped_at_100(): void
    {
        $result = $this->service->calculateFromOnboarding([
            'work_experience'       => '3+y',
            'education_level'       => 's1_done',
            'exploration_readiness' => 'very_comfortable',
            'support_level'         => 'strong',
        ]);

        $this->assertLessThanOrEqual(100, $result['score']);
    }

    public function test_all_signals_combined(): void
    {
        $result = $this->service->calculateFromOnboarding([
            'work_experience'       => '3+y',       // +25
            'education_level'       => 's1_done',    // +15
            'exploration_readiness' => 'very_comfortable', // +20
            'support_level'         => 'strong',     // +10
        ]);
        // 30 + 25 + 15 + 20 + 10 = 100
        $this->assertEquals(100, $result['score']);
        $this->assertEquals('high', $result['level']);
    }

    public function test_behavior_signals_login_count(): void
    {
        $base = $this->service->calculateFromOnboarding([]);
        $withLogin = $this->service->calculateFromOnboarding([
            'behavior_signals' => ['login_count' => 5],
        ]);

        $this->assertEquals(30, $base['score']);
        $this->assertEquals(33, $withLogin['score']); // +3 for 5+ logins
    }

    public function test_behavior_signals_skills_completed(): void
    {
        $withSkills = $this->service->calculateFromOnboarding([
            'behavior_signals' => ['skills_completed' => 3],
        ]);

        $this->assertEquals(35, $withSkills['score']); // 30 + 5
    }

    public function test_update_for_user_creates_context_score(): void
    {
        $user = User::factory()->create();

        $this->service->updateForUser($user->id, [
            'score'   => 55,
            'level'   => 'medium',
            'factors' => ['work_experience' => '1-2y'],
        ]);

        $cs = ContextScore::where('user_id', $user->id)->first();
        $this->assertNotNull($cs);
        $this->assertEquals(55, $cs->score);
        $this->assertEquals('medium', $cs->level);
        $this->assertEquals('1-2y', $cs->factors['work_experience']);
    }

    public function test_update_for_user_merges_behavior_signals(): void
    {
        $user = User::factory()->create();

        // First update
        $this->service->updateForUser($user->id, [
            'score'   => 33,
            'level'   => 'low',
            'factors' => ['behavior_signals' => ['login_count' => 3]],
        ]);

        // Second update
        $this->service->updateForUser($user->id, [
            'score'   => 35,
            'level'   => 'low',
            'factors' => ['behavior_signals' => ['skills_completed' => 1]],
        ]);

        $cs = ContextScore::where('user_id', $user->id)->first();
        $this->assertEquals(3, $cs->factors['behavior_signals']['login_count']);
        $this->assertEquals(1, $cs->factors['behavior_signals']['skills_completed']);
    }
}
