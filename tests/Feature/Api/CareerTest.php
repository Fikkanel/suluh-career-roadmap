<?php

namespace Tests\Feature\Api;

use App\Models\Career;
use App\Models\Skill;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CareerTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_careers_with_api_key(): void
    {
        Career::factory()->count(3)->create(['is_active' => true]);

        $response = $this->withHeaders([
            'X-API-KEY' => 'suluh-api-key-2024',
        ])->getJson('/api/v1/careers');

        $response->assertStatus(200)
            ->assertJsonStructure(['careers']);
    }

    public function test_list_careers_without_api_key(): void
    {
        $response = $this->getJson('/api/v1/careers');

        $response->assertStatus(401);
    }

    public function test_list_careers_with_invalid_api_key(): void
    {
        $response = $this->withHeaders([
            'X-API-KEY' => 'wrong-key',
        ])->getJson('/api/v1/careers');

        $response->assertStatus(401);
    }
}
