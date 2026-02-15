<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConsumptionEntry;
use App\Models\User;
use App\Models\EmissionFactorItem;
use App\Services\ClimatiqService;
use Illuminate\Support\Facades\Validator;

class ConsumptionEntryController extends BaseController
{
    protected ClimatiqService $climatiqService;

    public function __construct(ClimatiqService $climatiqService)
    {
        $this->climatiqService = $climatiqService;
    }
  
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'factor_items_id'  => 'required|integer|exists:emission_factor_items,id',
            'quantity'         => 'required|numeric|min:0.1',
            'entry_date'       => 'required|date',
            'image'            => 'nullable|file|image|max:2048',
            'metadata'         => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return $this->validation($validator->errors());
        }

        $userId = $request->custom_user_id;
        $user = User::find($userId);
        $factor = EmissionFactorItem::find($request->factor_items_id);

        if (!$user || !$factor) {
            return $this->notFound('User atau faktor emisi tidak ditemukan');
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('consumption_images', 'public');
        }

        $defaultMetadata = [
            'customEfficiency'    => null,
            'fuelType'            => "",
            'transportationMode'  => "",
            'useCustomEfficiency' => false,
            'vehicleType'         => "",  // Added for fuel & transport calculations
        ];

        $requestMetadata = $request->input('metadata', []);
        if (!is_array($requestMetadata)) {
            $requestMetadata = [];
        }
        $metadata = array_merge($defaultMetadata, $requestMetadata);

        // ============================================
        // CALCULATE EMISSIONS
        // ============================================
        // Different logic based on category:
        // 1. Food & Packaging: Fixed value OR Climatiq API
        // 2. Fuel Consumption: (distance / efficiency) × fuel_factor
        // 3. Public Transport: (emission_factor × distance) / avg_passengers
        // 4. Vehicle Efficiency: Not calculated (reference data only)
        
        $emissions = 0;
        $categoryName = $factor->category->category_name ?? '';
        
        // ============================================
        // CATEGORY 1: FOOD & PACKAGING
        // ============================================
        if (str_contains(strtolower($categoryName), 'food') || 
            str_contains(strtolower($categoryName), 'packaging')) {
            
            // Check if Climatiq ID exists
            if (!empty($factor->climatiq_id)) {
                // Use Climatiq API
                $result = $this->climatiqService->calculateEmissions(
                    climatiqId: $factor->climatiq_id,
                    quantity: (float) $request->quantity,
                    unit: 'kg',
                    parameterType: 'weight'
                );
                
                if ($result['success']) {
                    $emissions = $result['co2e'];
                } else {
                    // Fallback to value if available
                    if (is_numeric($factor->value)) {
                        $emissions = $request->quantity * floatval($factor->value);
                    }
                }
            } else {
                // Use fixed value calculation
                if (is_numeric($factor->value)) {
                    $emissions = $request->quantity * floatval($factor->value);
                }
            }
        }
        
        // ============================================
        // CATEGORY 2: FUEL CONSUMPTION (PRIVATE VEHICLE)
        // ============================================
        elseif (str_contains(strtolower($categoryName), 'fuel')) {
            
            $distance = (float) $request->quantity; // in km
            $fuelEmissionFactor = floatval($factor->value); // kg CO2e/liter
            
            // Get efficiency (custom or default)
            $efficiency = 20; // default fallback
            
            if (!empty($metadata['useCustomEfficiency']) && !empty($metadata['customEfficiency'])) {
                // Use custom efficiency from user input
                $efficiency = (float) $metadata['customEfficiency'];
            } else {
                // Get default efficiency from vehicle type
                if (!empty($metadata['vehicleType'])) {
                    $vehicleItem = EmissionFactorItem::whereHas('category', function($q) {
                        $q->where('category_name', 'Vehicle Efficiency');
                    })->where('name', $metadata['vehicleType'])->first();
                    
                    if ($vehicleItem && is_numeric($vehicleItem->value)) {
                        $efficiency = floatval($vehicleItem->value);
                    }
                }
            }
            
            // Calculate: (distance / efficiency) × fuel_emission_factor
            $litersUsed = $distance / $efficiency;
            $emissions = $litersUsed * $fuelEmissionFactor;
        }
        
        // ============================================
        // CATEGORY 3: PUBLIC TRANSPORT
        // ============================================
        elseif (str_contains(strtolower($categoryName), 'transport')) {
            
            $distance = (float) $request->quantity; // in km
            
            // Check if this is emission factor or passenger count
            $isEmissionFactor = str_contains($factor->name, '(Emission)');
            
            if ($isEmissionFactor) {
                // This is the emission factor
                $emissionFactor = floatval($factor->value); // kg CO2e/km (total vehicle)
                
                // Get passenger count
                // Name format: "City Bus (Emission)" -> find "City Bus (Passengers)"
                $vehicleBaseName = str_replace(' (Emission)', '', $factor->name);
                $passengerItem = EmissionFactorItem::where('factor_category_id', $factor->factor_category_id)
                    ->where('name', $vehicleBaseName . ' (Passengers)')
                    ->first();
                
                if ($passengerItem && is_numeric($passengerItem->value)) {
                    $avgPassengers = floatval($passengerItem->value);
                    
                    // Calculate: (emission_factor × distance) / avg_passengers
                    $emissions = ($emissionFactor * $distance) / $avgPassengers;
                } else {
                    // Fallback: assume 1 passenger if not found
                    $emissions = $emissionFactor * $distance;
                }
            } else {
                // This is passenger count data, shouldn't be used for entry
                // Set emissions to 0 or throw error
                $emissions = 0;
            }
        }
        
        // ============================================
        // CATEGORY 4: VEHICLE EFFICIENCY
        // ============================================
        else {
            // Vehicle efficiency is reference data, not for consumption entry
            // Use simple multiplication as fallback
            if (is_numeric($factor->value)) {
                $emissions = $request->quantity * floatval($factor->value);
            }
        }


        $entry = ConsumptionEntry::create([
            'user_id'         => $userId,
            'factor_items_id' => $request->factor_items_id,
            'entry_date'      => $request->entry_date,
            'quantity'        => $request->quantity,
            'emissions'       => $emissions,
            'image'           => $imagePath,
            'metadata'        => $metadata,
            'createdAt'       => now()
        ]);

        $entry->load('factorItem.category');

        return $this->success(
            $entry,
            'Entri konsumsi berhasil dibuat',
            201
        );
    }

    public function index(Request $request)
    {
        $userId = $request->custom_user_id;

        $query = ConsumptionEntry::with('factorItem.category')
            ->where('user_id', $userId);

        // Filter by single date (exact day)
        if ($request->has('date')) {
            $query->whereDate('entry_date', $request->date);
        }

        // Filter by date range
        if ($request->has('start_date')) {
            $query->whereDate('entry_date', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->whereDate('entry_date', '<=', $request->end_date);
        }

        $entries = $query->orderBy('entry_date', 'desc')->get();

        if ($entries->isEmpty()) {
            return $this->success(
                [],
                'Tidak ada data konsumsi untuk tanggal ini'
            );
        }

        return $this->success(
            $entries,
            'Data konsumsi berhasil diambil'
        );
    }

    public function show($id)
    {
        $entry = ConsumptionEntry::with('factorItem.category')->find($id);

        if (!$entry) {
            return $this->notFound('Entri konsumsi tidak ditemukan');
        }

        return $this->success($entry);
    }

    public function update(Request $request, $id)
    {
        $entry = ConsumptionEntry::find($id);

        if (!$entry) {
            return $this->notFound('Entri konsumsi tidak ditemukan');
        }

        $validator = Validator::make($request->all(), [
            'factor_items_id'  => 'sometimes|integer|exists:emission_factor_items,id',
            'quantity'         => 'sometimes|numeric|min:0.1',
            'entry_date'       => 'sometimes|date',
            'image'            => 'nullable|file|image|max:2048',
            'metadata'         => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return $this->validation($validator->errors());
        }

        // Update image if provided
        if ($request->hasFile('image')) {
            $entry->image = $request->file('image')->store('consumption_images', 'public');
        }

        // Update basic fields
        if ($request->has('factor_items_id')) {
            $entry->factor_items_id = $request->factor_items_id;
        }
        if ($request->has('quantity')) {
            $entry->quantity = $request->quantity;
        }
        if ($request->has('entry_date')) {
            $entry->entry_date = $request->entry_date;
        }

        // Update metadata
        $defaultMetadata = [
            'customEfficiency'    => null,
            'fuelType'            => "",
            'transportationMode'  => "",
            'useCustomEfficiency' => false,
            'vehicleType'         => "",
        ];

        $requestMetadata = $request->input('metadata', []);
        if (!is_array($requestMetadata)) {
            $requestMetadata = [];
        }
        $existingMetadata = is_array($entry->metadata) ? $entry->metadata : $defaultMetadata;
        $metadata = array_merge($existingMetadata, $requestMetadata);
        $entry->metadata = $metadata;

        // Recalculate emissions
        $factor = EmissionFactorItem::find($entry->factor_items_id);
        if ($factor) {
            $emissions = 0;
            $categoryName = $factor->category->category_name ?? '';

            // FOOD & PACKAGING
            if (str_contains(strtolower($categoryName), 'food') || 
                str_contains(strtolower($categoryName), 'packaging')) {
                
                if (!empty($factor->climatiq_id)) {
                    $result = $this->climatiqService->calculateEmissions(
                        climatiqId: $factor->climatiq_id,
                        quantity: (float) $entry->quantity,
                        unit: 'kg',
                        parameterType: 'weight'
                    );
                    if ($result['success']) {
                        $emissions = $result['co2e'];
                    } elseif (is_numeric($factor->value)) {
                        $emissions = $entry->quantity * floatval($factor->value);
                    }
                } elseif (is_numeric($factor->value)) {
                    $emissions = $entry->quantity * floatval($factor->value);
                }
            }
            // FUEL CONSUMPTION
            elseif (str_contains(strtolower($categoryName), 'fuel')) {
                $distance = (float) $entry->quantity;
                $fuelEmissionFactor = floatval($factor->value);
                
                $efficiency = 20;
                $useCustom = $metadata['useCustomEfficiency'] ?? false;
                if (is_string($useCustom)) {
                    $useCustom = in_array(strtolower($useCustom), ['true', '1', 'yes']);
                }
                
                if ($useCustom && !empty($metadata['customEfficiency']) && (float) $metadata['customEfficiency'] > 0) {
                    $efficiency = (float) $metadata['customEfficiency'];
                } else {
                    if (!empty($metadata['vehicleType'])) {
                        $vehicleItem = EmissionFactorItem::whereHas('category', function($q) {
                            $q->where('category_name', 'Vehicle Efficiency');
                        })->where('name', $metadata['vehicleType'])->first();
                        if ($vehicleItem && is_numeric($vehicleItem->value)) {
                            $efficiency = floatval($vehicleItem->value);
                        }
                    }
                }
                if ($efficiency <= 0) $efficiency = 20;
                
                $emissions = ($distance / $efficiency) * $fuelEmissionFactor;
            }
            // PUBLIC TRANSPORT
            elseif (str_contains(strtolower($categoryName), 'transport')) {
                $distance = (float) $entry->quantity;
                $isEmissionFactor = str_contains($factor->name, '(Emission)');
                
                if ($isEmissionFactor) {
                    $emissionFactor = floatval($factor->value);
                    $vehicleBaseName = str_replace(' (Emission)', '', $factor->name);
                    $passengerItem = EmissionFactorItem::where('factor_category_id', $factor->factor_category_id)
                        ->where('name', $vehicleBaseName . ' (Passengers)')
                        ->first();
                    
                    if ($passengerItem && is_numeric($passengerItem->value)) {
                        $avgPassengers = floatval($passengerItem->value);
                        $emissions = ($emissionFactor * $distance) / $avgPassengers;
                    } else {
                        $emissions = $emissionFactor * $distance;
                    }
                }
            }
            // FALLBACK
            else {
                if (is_numeric($factor->value)) {
                    $emissions = $entry->quantity * floatval($factor->value);
                }
            }

            $entry->emissions = $emissions;
        }

        $entry->save();
        $entry->load('factorItem.category');

        return $this->success(
            $entry,
            'Entri konsumsi berhasil diperbarui'
        );
    }

    public function destroy($id)
    {
        $entry = ConsumptionEntry::find($id);

        if (!$entry) {
            return $this->notFound('Entri konsumsi tidak ditemukan');
        }

        $entry->delete();

        return $this->success(
            null,
            'Entri konsumsi berhasil dihapus'
        );
    }
}
