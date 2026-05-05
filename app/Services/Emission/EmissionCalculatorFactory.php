<?php

namespace App\Services\Emission;

use App\Services\Emission\Contracts\EmissionCalculatorInterface;
use App\Services\Emission\Calculators\FoodEmissionCalculator;
use App\Services\Emission\Calculators\PrivateVehicleEmissionCalculator;
use App\Services\Emission\Calculators\PublicTransitEmissionCalculator;

class EmissionCalculatorFactory
{
    /**
     * Kembalikan kalkulator yang tepat berdasarkan entry_type.
     *
     * Menerapkan Strategy Pattern — setiap tipe emisi punya implementasinya sendiri.
     *
     * @param  string  $entryType  'food' | 'private_vehicle' | 'public_transit'
     * @throws \InvalidArgumentException jika entry_type tidak dikenal
     */
    public static function make(string $entryType): EmissionCalculatorInterface
    {
        return match ($entryType) {
            'food'             => app(FoodEmissionCalculator::class),
            'private_vehicle'  => app(PrivateVehicleEmissionCalculator::class),
            'public_transit'   => app(PublicTransitEmissionCalculator::class),
            default            => throw new \InvalidArgumentException(
                                    "Entry type tidak dikenal: '{$entryType}'. " .
                                    "Nilai yang valid: food, private_vehicle, public_transit."
                                ),
        };
    }
}
