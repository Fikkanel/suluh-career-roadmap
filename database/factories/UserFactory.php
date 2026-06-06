<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'age_range'             => '18-22',
            'education_level'       => 's1_ongoing',
            'major'                 => 'Teknik Informatika',
            'university_name'       => 'Universitas Negeri Malang',
            'work_experience'       => 'none',
            'province'              => 'DKI Jakarta',
            'exploration_readiness' => 'comfortable',
            'support_level'         => 'moderate',
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function withoutOnboarding(): static
    {
        return $this->state(fn (array $attributes) => [
            'age_range'             => null,
            'education_level'       => null,
            'major'                 => null,
            'university_name'       => null,
            'work_experience'       => null,
            'province'              => null,
            'exploration_readiness' => null,
            'support_level'         => null,
        ]);
    }

    public function withProvince(string $province = 'DKI Jakarta'): static
    {
        return $this->state(fn (array $attributes) => [
            'province' => $province,
        ]);
    }

    public function withReadiness(string $readiness = 'comfortable'): static
    {
        return $this->state(fn (array $attributes) => [
            'exploration_readiness' => $readiness,
        ]);
    }

    public function withSupportLevel(string $support = 'moderate'): static
    {
        return $this->state(fn (array $attributes) => [
            'support_level' => $support,
        ]);
    }

    public function withOnboardingData(): static
    {
        return $this->state(fn (array $attributes) => [
            'age_range'             => '18-22',
            'education_level'       => 's1_ongoing',
            'major'                 => 'Teknik Informatika',
            'university_name'       => 'Universitas Negeri Malang',
            'work_experience'       => 'none',
            'province'              => 'DKI Jakarta',
            'exploration_readiness' => 'comfortable',
            'support_level'         => 'moderate',
        ]);
    }
}
