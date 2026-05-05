<?php

namespace App\Services\Emission\Calculators;

use App\Models\VehicleType;
use App\Models\FuelType;
use App\Services\Emission\Contracts\EmissionCalculatorInterface;

class PrivateVehicleEmissionCalculator implements EmissionCalculatorInterface
{
    /**
     * Hitung emisi untuk kendaraan pribadi.
     *
     * Formula: emissions = (km / efficiency) × fuel_emission_factor
     *
     * - efficiency: ambil dari custom_efficiency jika ada, lain pakai default kendaraan
     * - fuel_emission_factor: dari tabel fuel_types (kgCO2e/liter)
     */
    public function calculate(array $input): float
    {
        $vehicle  = VehicleType::findOrFail($input['vehicle_type_id']);
        $fuel     = FuelType::findOrFail($input['fuel_type_id']);
        $km       = (float) $input['quantity'];

        $efficiency = (isset($input['custom_efficiency']) && (float) $input['custom_efficiency'] > 0)
            ? (float) $input['custom_efficiency']
            : $vehicle->default_efficiency;

        if ($efficiency <= 0) {
            throw new \InvalidArgumentException(
                "Efisiensi kendaraan harus lebih dari 0. Cek data kendaraan ID: {$vehicle->id}"
            );
        }

        $litersConsumed = $km / $efficiency;

        return $litersConsumed * $fuel->emission_factor;
    }
}
