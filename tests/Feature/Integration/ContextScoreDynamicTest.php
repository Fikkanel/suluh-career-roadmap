<?php

namespace Tests\Feature\Integration;

use App\Models\User;
use App\Models\Career;
use App\Models\Skill;
use App\Models\ContextScore;
use App\Models\UserProgress;
use App\Events\CareerChosen;
use App\Events\SkillStatusUpdated;
use App\Events\UserLoggedIn;
use App\Listeners\UpdateContextScoreFromCareerChosen;
use App\Listeners\UpdateContextScoreFromSkillProgress;
use App\Listeners\UpdateContextScoreFromLogin;
use App\Services\ContextScoreService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ContextScoreDynamicTest extends TestCase
{
    use RefreshDatabase;

    public function test_career_chosen_event_updates_context_score(): void
    {
        $user = User::factory()->create([
            'work_experience'       => '1-2y',
            'education_level'       => 's1_done',
            'exploration_readiness' => 'comfortable',
            'support_level'         => 'moderate',
        ]);
        $career = Career::factory()->create();

        $listener = new UpdateContextScoreFromCareerChosen(new ContextScoreService());
        $listener->handle(new CareerChosen($user, $career));

        $cs = ContextScore::where('user_id', $user->id)->first();
        $this->assertNotNull($cs);
        $this->assertArrayHasKey('career_chosen_at', $cs->factors['behavior_signals'] ?? []);
        $this->assertEquals(1, $cs->factors['behavior_signals']['career_chosen_count'] ?? 0);
    }

    public function test_skill_status_updated_event_updates_context_score(): void
    {
        $user = User::factory()->withoutOnboarding()->create([
            'work_experience'       => 'none',
            'exploration_readiness' => 'cautious',
        ]);
        $career = Career::factory()->create();
        $skill = Skill::factory()->create(['career_id' => $career->id]);

        $listener = new UpdateContextScoreFromSkillProgress(new ContextScoreService());
        $listener->handle(new SkillStatusUpdated($user, $skill, 'not_started', 'done'));

        $cs = ContextScore::where('user_id', $user->id)->first();
        $this->assertNotNull($cs);
        $this->assertEquals(1, $cs->factors['behavior_signals']['skills_progressed'] ?? 0);
        $this->assertEquals(1, $cs->factors['behavior_signals']['skills_completed'] ?? 0);
    }

    public function test_user_logged_in_event_updates_context_score(): void
    {
        $user = User::factory()->withoutOnboarding()->create([
            'work_experience' => 'none',
        ]);

        $listener = new UpdateContextScoreFromLogin(new ContextScoreService());
        $listener->handle(new UserLoggedIn($user));

        $cs = ContextScore::where('user_id', $user->id)->first();
        $this->assertNotNull($cs);
        $this->assertEquals(1, $cs->factors['behavior_signals']['login_count'] ?? 0);
        $this->assertArrayHasKey('last_login', $cs->factors['behavior_signals'] ?? []);
    }

    public function test_multiple_logins_accumulate(): void
    {
        $user = User::factory()->withoutOnboarding()->create(['work_experience' => 'none']);
        $listener = new UpdateContextScoreFromLogin(new ContextScoreService());

        for ($i = 0; $i < 3; $i++) {
            $listener->handle(new UserLoggedIn($user));
        }

        $cs = ContextScore::where('user_id', $user->id)->first();
        $this->assertEquals(3, $cs->factors['behavior_signals']['login_count'] ?? 0);
    }

    public function test_multiple_skill_updates_accumulate(): void
    {
        $user = User::factory()->withoutOnboarding()->create(['work_experience' => 'none']);
        $career = Career::factory()->create();
        $skill1 = Skill::factory()->create(['career_id' => $career->id]);
        $skill2 = Skill::factory()->create(['career_id' => $career->id]);

        $listener = new UpdateContextScoreFromSkillProgress(new ContextScoreService());
        $listener->handle(new SkillStatusUpdated($user, $skill1, 'not_started', 'learning'));
        $listener->handle(new SkillStatusUpdated($user, $skill2, 'not_started', 'done'));

        $cs = ContextScore::where('user_id', $user->id)->first();
        $this->assertEquals(2, $cs->factors['behavior_signals']['skills_progressed'] ?? 0);
        $this->assertEquals(1, $cs->factors['behavior_signals']['skills_completed'] ?? 0);
    }

    public function test_behavior_signals_affect_score(): void
    {
        $user = User::factory()->withoutOnboarding()->create(['work_experience' => 'none']);
        $listener = new UpdateContextScoreFromLogin(new ContextScoreService());

        // 5+ logins should add +3 to score
        for ($i = 0; $i < 5; $i++) {
            $listener->handle(new UserLoggedIn($user));
        }

        $cs = ContextScore::where('user_id', $user->id)->first();
        // Base 30 + 3 (login bonus) = 33
        $this->assertEquals(33, $cs->score);
    }

    public function test_event_dispatched_on_login(): void
    {
        Event::fake([UserLoggedIn::class]);

        $user = User::factory()->create();

        // Simulate login event
        event(new UserLoggedIn($user));

        Event::assertDispatched(UserLoggedIn::class, function ($event) use ($user) {
            return $event->user->id === $user->id;
        });
    }

    public function test_event_dispatched_on_career_chose(): void
    {
        Event::fake([CareerChosen::class]);

        $user = User::factory()->create();
        $career = Career::factory()->create();

        event(new CareerChosen($user, $career));

        Event::assertDispatched(CareerChosen::class, function ($event) use ($user, $career) {
            return $event->user->id === $user->id && $event->career->id === $career->id;
        });
    }

    public function test_event_dispatched_on_skill_status_update(): void
    {
        Event::fake([SkillStatusUpdated::class]);

        $user = User::factory()->create();
        $skill = Skill::factory()->create();

        event(new SkillStatusUpdated($user, $skill, 'not_started', 'learning'));

        Event::assertDispatched(SkillStatusUpdated::class, function ($event) use ($user, $skill) {
            return $event->user->id === $user->id
                && $event->skill->id === $skill->id
                && $event->oldStatus === 'not_started'
                && $event->newStatus === 'learning';
        });
    }
}
