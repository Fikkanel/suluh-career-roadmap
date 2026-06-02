<?php

namespace Database\Factories;

use App\Models\Skill;
use App\Models\Career;
use Illuminate\Database\Eloquent\Factories\Factory;

class SkillFactory extends Factory
{
    protected $model = Skill::class;

    public function definition(): array
    {
        return [
            'career_id'       => Career::factory(),
            'name'            => fake()->unique()->words(3, true),
            'level'           => fake()->randomElement(['beginner', 'intermediate', 'advanced']),
            'estimated_hours' => fake()->numberBetween(10, 80),
            'resources'       => null,
            'order'           => fake()->numberBetween(1, 20),
        ];
    }
}
