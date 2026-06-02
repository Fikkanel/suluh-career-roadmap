<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImpactTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_impact_with_api_key(): void
    {
        $response = $this->withHeaders([
            'X-API-KEY' => 'suluh-api-key-2024',
        ])->getJson('/api/v1/impact/public');

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['active_users', 'total_users', 'avg_crs', 'positive_change_pct', 'pivot_rate']]);
    }

    public function test_public_impact_without_api_key(): void
    {
        $response = $this->getJson('/api/v1/impact/public');

        $response->assertStatus(401);
    }
}
