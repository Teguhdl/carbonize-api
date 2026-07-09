<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TransitVehicle>
 */
class TransitVehicleFactory extends Factory
{
    public function definition(): array
    {
        $vehicles = ['Bus TransJakarta', 'MRT', 'KRL', 'Angkot', 'Ojek Online'];

        return [
            'name'            => fake()->randomElement($vehicles),
            'emission_factor' => fake()->randomFloat(4, 0.05, 1.5),
            'avg_passengers'  => fake()->numberBetween(10, 150),
        ];
    }
}
