<?php

namespace App\Services\Emission\Contracts;

use App\Models\ConsumptionEntry;

interface EmissionCalculatorInterface
{
    /**
     * Hitung emisi karbon berdasarkan data input dan kembalikan nilai float kgCO2e.
     *
     * @param  array  $input  Data dari request (quantity, ids, dll.)
     * @return float          Nilai emisi dalam kgCO2e
     */
    public function calculate(array $input): float;
}
