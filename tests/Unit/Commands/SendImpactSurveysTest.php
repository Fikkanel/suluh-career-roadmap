<?php

namespace Tests\Unit\Commands;

use App\Models\User;
use App\Models\Career;
use App\Models\ImpactSurvey;
use App\Models\UserProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SendImpactSurveysTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_3_month_survey_for_eligible_users(): void
    {
        $career = Career::factory()->create(['is_active' => true]);
        $user = User::factory()->create([
            'current_career_id' => $career->id,
            'created_at'        => now()->subMonths(4),
        ]);

        $this->artisan('surveys:send');

        $this->assertDatabaseHas('impact_surveys', [
            'user_id' => $user->id,
            'type'    => '3_months',
        ]);
    }

    public function test_creates_6_month_survey_for_eligible_users(): void
    {
        $career = Career::factory()->create(['is_active' => true]);
        $user = User::factory()->create([
            'current_career_id' => $career->id,
            'created_at'        => now()->subMonths(7),
        ]);

        $this->artisan('surveys:send');

        $this->assertDatabaseHas('impact_surveys', [
            'user_id' => $user->id,
            'type'    => '6_months',
        ]);
    }

    public function test_skips_users_without_career(): void
    {
        $user = User::factory()->create([
            'current_career_id' => null,
            'created_at'        => now()->subMonths(4),
        ]);

        $this->artisan('surveys:send');

        $this->assertDatabaseMissing('impact_surveys', [
            'user_id' => $user->id,
        ]);
    }

    public function test_does_not_create_duplicates(): void
    {
        $career = Career::factory()->create(['is_active' => true]);
        $user = User::factory()->create([
            'current_career_id' => $career->id,
            'created_at'        => now()->subMonths(4),
        ]);

        ImpactSurvey::create([
            'user_id' => $user->id,
            'type'    => '3_months',
        ]);

        $this->artisan('surveys:send');

        $this->assertEquals(1, ImpactSurvey::where('user_id', $user->id)
            ->where('type', '3_months')
            ->count());
    }

    public function test_skips_admin_users(): void
    {
        $career = Career::factory()->create(['is_active' => true]);
        $admin = User::factory()->create([
            'is_admin'          => true,
            'current_career_id' => $career->id,
            'created_at'        => now()->subMonths(4),
        ]);

        $this->artisan('surveys:send');

        $this->assertDatabaseMissing('impact_surveys', [
            'user_id' => $admin->id,
        ]);
    }

    public function test_records_crs_before_at_creation(): void
    {
        $career = Career::factory()->create(['is_active' => true]);
        $user = User::factory()->create([
            'current_career_id' => $career->id,
            'created_at'        => now()->subMonths(4),
        ]);

        $skill = \App\Models\Skill::factory()->create(['career_id' => $career->id]);
        UserProgress::create([
            'user_id'  => $user->id,
            'skill_id' => $skill->id,
            'status'   => 'done',
        ]);

        $this->artisan('surveys:send');

        $survey = ImpactSurvey::where('user_id', $user->id)->where('type', '3_months')->first();
        $this->assertNotNull($survey);
        $this->assertNotNull($survey->crs_before);
    }

    public function test_skips_users_not_past_milestone(): void
    {
        $career = Career::factory()->create(['is_active' => true]);
        $user = User::factory()->create([
            'current_career_id' => $career->id,
            'created_at'        => now()->subMonths(1),
        ]);

        $this->artisan('surveys:send');

        $this->assertDatabaseMissing('impact_surveys', [
            'user_id' => $user->id,
        ]);
    }
}
