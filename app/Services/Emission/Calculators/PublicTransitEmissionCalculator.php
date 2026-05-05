<?php

namespace App\Services\Emission\Calculators;

use App\Models\TransitVehicle;
use App\Services\Emission\Contracts\EmissionCalculatorInterface;

class PublicTransitEmissionCalculator implements EmissionCalculatorInterface
{
    /**
     * Hitung emisi untuk transportasi umum.
     *
     * Formula: emissions = (emission_factor × km) / avg_passengers
     *
     * - emission_factor: kgCO2e per km untuk seluruh kendaraan
     * - avg_passengers: rata-rata penumpang per perjalanan
     */
    public function calculate(array $input): float
    {
        $vehicle = TransitVehicle::findOrFail($input['transit_vehicle_id']);
        $km      = (float) $input['quantity'];

        if ($vehicle->avg_passengers <= 0) {
            throw new \InvalidArgumentException(
                "avg_passengers harus lebih dari 0. Cek data kendaraan ID: {$vehicle->id}"
            );
        }

        return ($vehicle->emission_factor * $km) / $vehicle->avg_passengers;
    }
}
