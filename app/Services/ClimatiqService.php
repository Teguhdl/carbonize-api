<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClimatiqService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.climatiq.api_key', env('CLIMATIQ_API_KEY'));
        $this->baseUrl = config('services.climatiq.base_url', env('CLIMATIQ_BASE_URL', 'https://api.climatiq.io/data/v1/estimate'));
    }

    /**
     * Calculate emissions using Climatiq API
     * 
     * @param string $climatiqId - The activity_id from Climatiq
     * @param float $quantity - The quantity value
     * @param string $unit - Unit of measurement (kg, kWh, km, l, etc.)
     * @param string $parameterType - Type of parameter (weight, energy, distance, volume)
     * @return array
     */
    public function calculateEmissions(
        string $climatiqId, 
        float $quantity, 
        string $unit = 'kg',
        string $parameterType = 'weight'
    ): array {
        try {
            $requestBody = [
                'emission_factor' => [
                    'activity_id' => $climatiqId,
                    'data_version' => '^22',
                ],
                'parameters' => [
                    $parameterType => $quantity,
                    "{$parameterType}_unit" => $unit,
                ],
            ];

            Log::info('Climatiq API Request', ['body' => $requestBody]);

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl, $requestBody);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('Climatiq API Response', ['data' => $data]);
                
                return [
                    'success' => true,
                    'co2e' => $data['co2e'] ?? 0,
                    'co2e_unit' => $data['co2e_unit'] ?? 'kg',
                    'emission_factor' => $data['emission_factor'] ?? null,
                ];
            }

            Log::error('Climatiq API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'error' => 'Failed to calculate emissions',
                'details' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('Climatiq API Exception', ['message' => $e->getMessage()]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Calculate fuel emissions with distance and efficiency
     * 
     * @param string $climatiqId - The fuel activity_id from Climatiq
     * @param float $distance - Distance in kilometers
     * @param float $efficiency - Fuel efficiency in km/l
     * @return array
     */
    public function calculateFuelEmissions(
        string $climatiqId,
        float $distance,
        float $efficiency
    ): array {
        // Calculate liters consumed: Liters = Distance / Efficiency
        $liters = $distance / $efficiency;

        try {
            $requestBody = [
                'emission_factor' => [
                    'activity_id' => $climatiqId,
                    'data_version' => '^22',
                ],
                'parameters' => [
                    'volume' => $liters,
                    'volume_unit' => 'l',
                ],
            ];

            Log::info('Climatiq Fuel API Request', [
                'body' => $requestBody,
                'distance' => $distance,
                'efficiency' => $efficiency,
                'liters' => $liters,
            ]);

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl, $requestBody);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('Climatiq Fuel API Response', ['data' => $data]);
                
                return [
                    'success' => true,
                    'co2e' => $data['co2e'] ?? 0,
                    'co2e_unit' => $data['co2e_unit'] ?? 'kg',
                    'liters_consumed' => $liters,
                    'emission_factor' => $data['emission_factor'] ?? null,
                ];
            }

            Log::error('Climatiq Fuel API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'error' => 'Failed to calculate fuel emissions',
                'details' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('Climatiq Fuel API Exception', ['message' => $e->getMessage()]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
