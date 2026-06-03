<?php

namespace Tests\Feature\Web;

use App\Models\User;
use App\Models\Career;
use App\Models\ImpactSurvey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SurveyTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $career = Career::factory()->create(['is_active' => true]);
        $this->user = User::factory()->create(['current_career_id' => $career->id]);
    }

    public function test_show_survey_requires_auth(): void
    {
        $response = $this->get('/survey/3_months');
        $response->assertRedirect('/login');
    }

    public function test_show_survey_invalid_type_returns_404(): void
    {
        $response = $this->actingAs($this->user)->get('/survey/invalid');
        $response->assertStatus(404);
    }

    public function test_show_survey_no_record_redirects_to_dashboard(): void
    {
        $response = $this->actingAs($this->user)->get('/survey/3_months');
        $response->assertRedirect('/dashboard');
    }

    public function test_show_survey_with_record_returns_200(): void
    {
        ImpactSurvey::create([
            'user_id' => $this->user->id,
            'type'    => '3_months',
        ]);

        $response = $this->actingAs($this->user)->get('/survey/3_months');
        $response->assertStatus(200);
        $response->assertSee('Survei Dampak');
    }

    public function test_store_survey_requires_employed(): void
    {
        ImpactSurvey::create(['user_id' => $this->user->id, 'type' => '3_months']);

        $response = $this->actingAs($this->user)->post('/survey/3_months', [
            'employed'        => '',
            'career_change'   => 'yes',
            'position_change' => 'no',
            'satisfaction'    => 4,
        ]);
        $response->assertSessionHasErrors('employed');
    }

    public function test_store_survey_requires_valid_employed(): void
    {
        ImpactSurvey::create(['user_id' => $this->user->id, 'type' => '3_months']);

        $response = $this->actingAs($this->user)->post('/survey/3_months', [
            'employed'        => 'invalid',
            'career_change'   => 'yes',
            'position_change' => 'no',
            'satisfaction'    => 4,
        ]);
        $response->assertSessionHasErrors('employed');
    }

    public function test_store_survey_success(): void
    {
        $career = Career::factory()->create(['is_active' => true]);
        $this->user->update(['current_career_id' => $career->id]);

        ImpactSurvey::create([
            'user_id'    => $this->user->id,
            'type'       => '3_months',
            'crs_before' => 30,
        ]);

        $response = $this->actingAs($this->user)->post('/survey/3_months', [
            'employed'        => 'yes',
            'career_change'   => 'no',
            'position_change' => 'yes',
            'satisfaction'    => 5,
            'feedback'        => 'Platform sangat membantu',
        ]);

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('success');

        $survey = ImpactSurvey::where('user_id', $this->user->id)->where('type', '3_months')->first();
        $this->assertNotNull($survey->submitted_at);
        $this->assertNotNull($survey->crs_after);
        $this->assertEquals('yes', $survey->answers['employed']);
    }

    public function test_store_survey_invalid_type_returns_404(): void
    {
        $response = $this->actingAs($this->user)->post('/survey/invalid', [
            'employed'        => 'yes',
            'career_change'   => 'no',
            'position_change' => 'no',
            'satisfaction'    => 3,
        ]);
        $response->assertStatus(404);
    }

    public function test_survey_view_transparency_explanation(): void
    {
        ImpactSurvey::create(['user_id' => $this->user->id, 'type' => '3_months']);

        $response = $this->actingAs($this->user)->get('/survey/3_months');
        $content = strtolower($response->getContent());

        // Constitution P2: transparency about data use
        $this->assertStringContainsString('mengapa kami bertanya ini', $content);
        $this->assertStringContainsString('anonim', $content);
        $this->assertStringContainsString('tidak akan dijual', $content);
    }

    public function test_show_6_month_survey(): void
    {
        ImpactSurvey::create(['user_id' => $this->user->id, 'type' => '6_months']);

        $response = $this->actingAs($this->user)->get('/survey/6_months');
        $response->assertStatus(200);
        $response->assertSee('Bulan ke-6');
    }

    public function test_already_submitted_shows_previous_answers(): void
    {
        ImpactSurvey::create([
            'user_id'      => $this->user->id,
            'type'         => '3_months',
            'answers'      => ['employed' => 'yes', 'satisfaction' => 5],
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->get('/survey/3_months');
        $response->assertSee('Survei Sudah Diisi');
    }
}
