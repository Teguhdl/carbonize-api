<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FuelType>
 */
class FuelTypeFactory extends Factory
{
    public function definition(): array
    {
        $fuels = ['Pertalite', 'Pertamax', 'Solar', 'Premium', 'Dexlite'];

        return [
            'name'            => fake()->randomElement($fuels),
            'emission_factor' => fake()->randomFloat(4, 0.5, 5.0),
        ];
    }
}
