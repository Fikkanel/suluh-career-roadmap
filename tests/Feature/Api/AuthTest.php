<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_success(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name'                  => 'Test User',
            'email'                 => 'test@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'user', 'token']);
    }

    public function test_register_validation_fails(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name'     => '',
            'email'    => 'not-email',
            'password' => 'short',
        ]);

        $response->assertStatus(422);
    }

    public function test_login_with_basic_auth(): void
    {
        $user = User::factory()->create([
            'email'    => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $credentials = base64_encode('test@example.com:password123');

        $response = $this->withHeaders([
            'Authorization' => 'Basic ' . $credentials,
        ])->postJson('/api/v1/auth/login');

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'user', 'token', 'token_type', 'expires_in']);
    }

    public function test_login_without_basic_auth(): void
    {
        $response = $this->postJson('/api/v1/auth/login');

        $response->assertStatus(401);
    }

    public function test_login_invalid_credentials(): void
    {
        User::factory()->create([
            'email'    => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $credentials = base64_encode('test@example.com:wrongpassword');

        $response = $this->withHeaders([
            'Authorization' => 'Basic ' . $credentials,
        ])->postJson('/api/v1/auth/login');

        $response->assertStatus(401);
    }

    public function test_logout_success(): void
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/v1/auth/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Logout berhasil.']);
    }
}
