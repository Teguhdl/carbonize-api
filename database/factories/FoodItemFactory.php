<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FoodItem>
 */
class FoodItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'               => fake()->words(3, true),
            'calculation_method' => 'fixed',
            'emission_factor'    => fake()->randomFloat(4, 0.1, 10.0),
            'climatiq_id'        => null,
        ];
    }

    public function climatiq(): static
    {
        return $this->state(fn (array $attributes) => [
            'calculation_method' => 'climatiq',
            'emission_factor'    => null,
            'climatiq_id'        => 'food_packaging-' . fake()->slug(2),
        ]);
    }
}
