<?php

namespace Tests\Feature\Api;

use App\Models\AssessmentQuestion;
use App\Models\Career;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Tests\TestCase;

class AssessmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_submit_assessment(): void
    {
        $career = Career::factory()->create(['riasec_code' => 'IRA', 'is_active' => true]);
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $q1 = AssessmentQuestion::factory()->create([
            'type'             => 'single_choice',
            'riasec_category'  => 'I',
            'big_five_trait'   => 'Openness',
            'weight'           => 1.0,
            'is_active'        => true,
        ]);
        $q2 = AssessmentQuestion::factory()->create([
            'type'             => 'scale',
            'riasec_category'  => 'R',
            'big_five_trait'   => 'Conscientiousness',
            'weight'           => 1.0,
            'is_active'        => true,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/v1/assessment/submit', [
            'answers' => [
                $q1->id => 'a',
                $q2->id => 7,
            ],
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'result' => ['id', 'riasec', 'big_five', 'top_careers']]);
    }

    public function test_submit_assessment_without_jwt(): void
    {
        $response = $this->postJson('/api/v1/assessment/submit', []);

        $response->assertStatus(401);
    }
}
