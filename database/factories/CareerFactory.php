<?php

namespace Database\Factories;

use App\Models\Career;
use Illuminate\Database\Eloquent\Factories\Factory;

class CareerFactory extends Factory
{
    protected $model = Career::class;

    public function definition(): array
    {
        return [
            'name'              => fake()->unique()->jobTitle(),
            'slug'              => fake()->unique()->slug(2),
            'description'       => fake()->paragraph(),
            'riasec_code'       => fake()->randomElement(['IRA', 'SEC', 'AES', 'RIC', 'ISR', 'ESC', 'IAR', 'CSR']),
            'industry_standard' => fake()->sentence(),
            'is_active'         => true,
        ];
    }
}
