<?php

namespace Database\Factories;

use App\Models\AssessmentQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssessmentQuestionFactory extends Factory
{
    protected $model = AssessmentQuestion::class;

    public function definition(): array
    {
        $type = fake()->randomElement(['single_choice', 'scale', 'text_reflection']);

        return [
            'prompt'           => fake()->unique()->sentence(),
            'context'          => null,
            'riasec_category'  => fake()->randomElement(['R', 'I', 'A', 'S', 'E', 'C']),
            'big_five_trait'   => fake()->randomElement(['Openness', 'Conscientiousness', 'Extraversion', 'Agreeableness', 'Neuroticism']),
            'weight'           => 1.0,
            'type'             => $type,
            'options'          => $type === 'single_choice' ? [
                'a' => fake()->sentence(4),
                'b' => fake()->sentence(4),
                'c' => fake()->sentence(4),
                'd' => fake()->sentence(4),
            ] : null,
            'is_active'        => true,
            'order'            => fake()->numberBetween(1, 30),
        ];
    }
}
