<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_profile(): void
    {
        $user = User::factory()->create([
            'name'  => 'Test User',
            'email' => 'test@example.com',
        ]);
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/user/profile');

        $response->assertStatus(200)
            ->assertJsonPath('profile.name', 'Test User')
            ->assertJsonPath('profile.email', 'test@example.com');
    }

    public function test_update_profile(): void
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/v1/user/profile', [
            'name'       => 'Updated Name',
            'age_range'  => '21-25',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('profile.name', 'Updated Name');
    }

    public function test_profile_without_jwt(): void
    {
        $response = $this->getJson('/api/v1/user/profile');

        $response->assertStatus(401);
    }
}
