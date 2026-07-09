<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VehicleType>
 */
class VehicleTypeFactory extends Factory
{
    public function definition(): array
    {
        $types = ['Motor', 'Mobil', 'Truk', 'Pickup', 'Bus Pribadi'];

        return [
            'name'               => fake()->randomElement($types) . ' ' . fake()->word(),
            'default_efficiency' => fake()->randomFloat(1, 8.0, 25.0),
        ];
    }
}
