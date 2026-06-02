<?php

namespace Database\Factories;

use App\Models\SkillValidation;
use App\Models\User;
use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

class SkillValidationFactory extends Factory
{
    protected $model = SkillValidation::class;

    public function definition(): array
    {
        return [
            'user_id'            => User::factory(),
            'skill_id'           => Skill::factory(),
            'type'               => fake()->randomElement(['scenario', 'reflection', 'behavior']),
            'response'           => fake()->paragraph(),
            'self_assessed_level' => fake()->numberBetween(1, 5),
            'validated_at'       => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
