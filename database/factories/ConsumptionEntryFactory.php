<?php

namespace Database\Factories;

use App\Models\FoodItem;
use App\Models\FuelType;
use App\Models\TransitVehicle;
use App\Models\User;
use App\Models\VehicleType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ConsumptionEntry>
 */
class ConsumptionEntryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'    => User::factory(),
            'entry_type' => 'food',
            'entry_date' => fake()->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
            'quantity'   => fake()->randomFloat(2, 0.1, 5.0),
            'emissions'  => fake()->randomFloat(4, 0.01, 10.0),
            'image'      => null,
            'metadata'   => [],
        ];
    }

    public function food(): static
    {
        return $this->state(fn (array $attributes) => [
            'entry_type'   => 'food',
            'food_item_id' => FoodItem::factory(),
        ]);
    }

    public function privateVehicle(): static
    {
        return $this->state(fn (array $attributes) => [
            'entry_type'      => 'private_vehicle',
            'vehicle_type_id' => VehicleType::factory(),
            'fuel_type_id'    => FuelType::factory(),
        ]);
    }

    public function publicTransit(): static
    {
        return $this->state(fn (array $attributes) => [
            'entry_type'         => 'public_transit',
            'transit_vehicle_id' => TransitVehicle::factory(),
        ]);
    }
}
