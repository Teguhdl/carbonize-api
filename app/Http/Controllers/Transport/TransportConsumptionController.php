<?php

namespace App\Http\Controllers\Transport;

use App\Http\Controllers\BaseController;
use App\Models\ConsumptionEntry;
use App\Services\Emission\EmissionCalculatorFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransportConsumptionController extends BaseController
{
    /**
     * POST /transport/entries
     *
     * Menangani dua mode: private_vehicle dan public_transit
     * dibedakan oleh field "mode" dalam request.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mode'               => 'required|in:private,public',
            'quantity'           => 'required|numeric|min:0.1',
            'entry_date'         => 'required|date',
            'image'              => 'nullable|file|image|max:2048',
            // Private vehicle
            'vehicle_type_id'    => 'required_if:mode,private|nullable|integer|exists:vehicle_types,id',
            'fuel_type_id'       => 'required_if:mode,private|nullable|integer|exists:fuel_types,id',
            'custom_efficiency'  => 'nullable|numeric|min:0.1',
            // Public transit
            'transit_vehicle_id' => 'required_if:mode,public|nullable|integer|exists:transit_vehicles,id',
        ]);

        if ($validator->fails()) {
            return $this->validation($validator->errors());
        }

        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('consumption_images', 'public')
            : null;

        $entryType = $request->mode === 'private' ? 'private_vehicle' : 'public_transit';

        $calculator = EmissionCalculatorFactory::make($entryType);
        $emissions  = $calculator->calculate($request->all());

        $entry = ConsumptionEntry::create([
            'user_id'            => $request->custom_user_id,
            'entry_type'         => $entryType,
            'entry_date'         => $request->entry_date,
            'quantity'           => $request->quantity,
            'emissions'          => $emissions,
            'image'              => $imagePath,
            'metadata'           => $request->metadata ?? [],
            // Private vehicle
            'vehicle_type_id'    => $request->vehicle_type_id,
            'fuel_type_id'       => $request->fuel_type_id,
            'custom_efficiency'  => $request->custom_efficiency,
            // Public transit
            'transit_vehicle_id' => $request->transit_vehicle_id,
        ]);

        $relations = $entryType === 'private_vehicle'
            ? ['vehicleType', 'fuelType']
            : ['transitVehicle'];

        return $this->success(
            $entry->load($relations),
            'Entri konsumsi transportasi berhasil dicatat',
            201
        );
    }
}
