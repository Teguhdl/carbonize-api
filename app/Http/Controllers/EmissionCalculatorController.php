<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ClimatiqService;
use Illuminate\Support\Facades\Validator;

class EmissionCalculatorController extends BaseController
{
    protected ClimatiqService $climatiqService;

    public function __construct(ClimatiqService $climatiqService)
    {
        $this->climatiqService = $climatiqService;
    }

    /**
     * Calculate emissions using Climatiq API
     * 
     * POST /api/v1/emission/calculate
     */
    public function calculate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'climatiq_id' => 'required|string',
            'quantity' => 'required|numeric|min:0.01',
            'unit' => 'nullable|string',
            'parameter_type' => 'nullable|string|in:weight,energy,distance,volume',
        ]);

        if ($validator->fails()) {
            return $this->validation($validator->errors());
        }

        $result = $this->climatiqService->calculateEmissions(
            climatiqId: $request->input('climatiq_id'),
            quantity: (float) $request->input('quantity'),
            unit: $request->input('unit', 'kg'),
            parameterType: $request->input('parameter_type', 'weight')
        );

        if (!$result['success']) {
            return $this->error($result['error'] ?? 'Gagal menghitung emisi', 400);
        }

        return $this->success([
            'co2e' => $result['co2e'],
            'co2e_unit' => $result['co2e_unit'],
        ], 'Berhasil menghitung emisi');
    }

    /**
     * Calculate fuel emissions with distance and efficiency
     * 
     * POST /api/v1/emission/calculate-fuel
     */
    public function calculateFuel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'climatiq_id' => 'required|string',
            'distance' => 'required|numeric|min:0.01',
            'efficiency' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return $this->validation($validator->errors());
        }

        $result = $this->climatiqService->calculateFuelEmissions(
            climatiqId: $request->input('climatiq_id'),
            distance: (float) $request->input('distance'),
            efficiency: (float) $request->input('efficiency')
        );

        if (!$result['success']) {
            return $this->error($result['error'] ?? 'Gagal menghitung emisi bahan bakar', 400);
        }

        return $this->success([
            'co2e' => $result['co2e'],
            'co2e_unit' => $result['co2e_unit'],
            'liters_consumed' => $result['liters_consumed'],
        ], 'Berhasil menghitung emisi bahan bakar');
    }
}
