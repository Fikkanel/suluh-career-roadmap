<?php

namespace Tests\Feature\Web;

use App\Models\User;
use App\Models\Skill;
use App\Models\SkillValidation;
use App\Models\Career;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SkillValidationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Skill $skill;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $career = Career::factory()->create(['is_active' => true]);
        $this->skill = Skill::factory()->create(['career_id' => $career->id]);
    }

    public function test_show_validation_form_requires_auth(): void
    {
        $response = $this->get("/skill/{$this->skill->id}/validate");
        $response->assertRedirect('/login');
    }

    public function test_show_validation_form_returns_200(): void
    {
        $response = $this->actingAs($this->user)->get("/skill/{$this->skill->id}/validate");
        $response->assertStatus(200);
        $response->assertSee($this->skill->name);
    }

    public function test_store_validation_requires_response(): void
    {
        $response = $this->actingAs($this->user)->post("/skill/{$this->skill->id}/validate", [
            'response' => '',
            'type'     => 'reflection',
        ]);
        $response->assertSessionHasErrors('response');
    }

    public function test_store_validation_requires_valid_type(): void
    {
        $response = $this->actingAs($this->user)->post("/skill/{$this->skill->id}/validate", [
            'response' => 'My experience',
            'type'     => 'invalid_type',
        ]);
        $response->assertSessionHasErrors('type');
    }

    public function test_store_validation_creates_record(): void
    {
        $response = $this->actingAs($this->user)->post("/skill/{$this->skill->id}/validate", [
            'response'            => 'I have been learning this for 2 weeks',
            'self_assessed_level' => 3,
            'type'                => 'reflection',
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('skill_validations', [
            'user_id'  => $this->user->id,
            'skill_id' => $this->skill->id,
            'type'     => 'reflection',
        ]);
    }

    public function test_store_validation_updates_existing(): void
    {
        SkillValidation::create([
            'user_id'     => $this->user->id,
            'skill_id'    => $this->skill->id,
            'type'        => 'reflection',
            'response'    => 'Old response',
            'validated_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->post("/skill/{$this->skill->id}/validate", [
            'response' => 'Updated response',
            'type'     => 'reflection',
        ]);

        $this->assertDatabaseHas('skill_validations', [
            'user_id'  => $this->user->id,
            'skill_id' => $this->skill->id,
            'response' => 'Updated response',
        ]);

        $this->assertEquals(1, SkillValidation::where('user_id', $this->user->id)
            ->where('skill_id', $this->skill->id)
            ->where('type', 'reflection')
            ->count());
    }

    public function test_show_displays_existing_validation(): void
    {
        SkillValidation::create([
            'user_id'     => $this->user->id,
            'skill_id'    => $this->skill->id,
            'type'        => 'reflection',
            'response'    => 'Previous reflection text',
            'validated_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->get("/skill/{$this->skill->id}/validate");
        $response->assertSee('Previous reflection text');
        $response->assertSee('Refleksi Sebelumnya');
    }

    public function test_skill_with_scenario_shows_scenario_section(): void
    {
        $this->skill->update([
            'validation_type'   => 'scenario',
            'scenario_question' => 'Bagaimana kamu menerapkan skill ini dalam proyek nyata?',
        ]);

        $response = $this->actingAs($this->user)->get("/skill/{$this->skill->id}/validate");
        $response->assertSee('Skenario');
        $response->assertSee('Bagaimana kamu menerapkan skill ini dalam proyek nyata?');
    }

    public function test_skill_without_scenario_hides_scenario_section(): void
    {
        $response = $this->actingAs($this->user)->get("/skill/{$this->skill->id}/validate");
        $response->assertDontSee('Skenario');
    }

    public function test_validation_view_no_guilt_language(): void
    {
        $response = $this->actingAs($this->user)->get("/skill/{$this->skill->id}/validate");
        $content = $response->getContent();

        // Constitution P3: no guilt language — check main content only
        $this->assertStringContainsString('Refleksi', $content);
        $this->assertStringContainsString('pengalaman', $content);
    }

    public function test_allows_all_validation_types(): void
    {
        foreach (['scenario', 'reflection', 'behavior'] as $type) {
            $response = $this->actingAs($this->user)->post("/skill/{$this->skill->id}/validate", [
                'response' => "Test response for {$type}",
                'type'     => $type,
            ]);
            $response->assertRedirect();
        }

        $this->assertEquals(3, SkillValidation::where('user_id', $this->user->id)
            ->where('skill_id', $this->skill->id)
            ->count());
    }
}
