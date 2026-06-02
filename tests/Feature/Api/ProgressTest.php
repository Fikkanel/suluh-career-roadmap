<?php

namespace Tests\Feature\Api;

use App\Models\Career;
use App\Models\Skill;
use App\Models\User;
use App\Models\UserProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Tests\TestCase;

class ProgressTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_progress(): void
    {
        $career = Career::factory()->create(['is_active' => true]);
        $skill = Skill::factory()->create(['career_id' => $career->id]);
        $user = User::factory()->create(['current_career_id' => $career->id]);
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->patchJson('/api/v1/progress/update', [
            'skill_id' => $skill->id,
            'status'   => 'learning',
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Status skill diperbarui.']);
    }

    public function test_update_progress_invalid_status(): void
    {
        $career = Career::factory()->create(['is_active' => true]);
        $skill = Skill::factory()->create(['career_id' => $career->id]);
        $user = User::factory()->create(['current_career_id' => $career->id]);
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->patchJson('/api/v1/progress/update', [
            'skill_id' => $skill->id,
            'status'   => 'invalid_status',
        ]);

        $response->assertStatus(422);
    }

    public function test_update_progress_without_jwt(): void
    {
        $response = $this->patchJson('/api/v1/progress/update', []);

        $response->assertStatus(401);
    }
}
