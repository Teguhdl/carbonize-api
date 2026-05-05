<?php

namespace App\Services\Emission\Calculators;

use App\Models\FoodItem;
use App\Services\Emission\Contracts\EmissionCalculatorInterface;
use App\Services\ClimatiqService;
use Illuminate\Support\Facades\Log;

class FoodEmissionCalculator implements EmissionCalculatorInterface
{
    public function __construct(private ClimatiqService $climatiq) {}

    /**
     * Hitung emisi untuk konsumsi makanan & packaging.
     *
     * - Jika calculation_method = 'climatiq' → gunakan Climatiq API
     * - Jika calculation_method = 'fixed'    → quantity × emission_factor
     *
     * Formula: emissions = quantity (kg) × emission_factor (kgCO2e/kg)
     */
    public function calculate(array $input): float
    {
        $item     = FoodItem::findOrFail($input['food_item_id']);
        $quantity = (float) $input['quantity'];

        if ($item->calculation_method === 'climatiq' && $item->climatiq_id) {
            $result = $this->climatiq->calculateEmissions(
                climatiqId: $item->climatiq_id,
                quantity: $quantity,
                unit: 'kg',
                parameterType: 'weight'
            );

            if ($result['success']) {
                return (float) $result['co2e'];
            }

            Log::warning('Climatiq API gagal untuk food item: ' . $item->name, [
                'climatiq_id' => $item->climatiq_id,
                'error'       => $result['error'] ?? 'unknown',
            ]);

            // Fallback ke fixed jika Climatiq gagal dan ada emission_factor
            if ($item->emission_factor !== null && $item->emission_factor > 0) {
                return $quantity * $item->emission_factor;
            }

            return 0.0;
        }

        // Fixed calculation
        return $quantity * (float) ($item->emission_factor ?? 0);
    }
}
