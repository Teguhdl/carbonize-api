<?php

namespace App\Http\Controllers\Transport;

use App\Http\Controllers\BaseController;
use App\Models\VehicleType;
use App\Models\FuelType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PrivateVehicleController extends BaseController
{
    /* --------------------------------------------------------------------------
     | VEHICLE TYPES
     -------------------------------------------------------------------------- */

    /**
     * GET /transport/private/vehicles
     */
    public function vehicles()
    {
        return $this->success(VehicleType::all(), 'Daftar tipe kendaraan berhasil diambil');
    }

    /**
     * POST /transport/private/vehicles
     */
    public function storeVehicle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'               => 'required|string|max:255',
            'default_efficiency' => 'required|numeric|min:0.1',
        ]);

        if ($validator->fails()) {
            return $this->validation($validator->errors());
        }

        $vehicle = VehicleType::create($request->only(['name', 'default_efficiency']));

        return $this->success($vehicle, 'Tipe kendaraan berhasil ditambahkan', 201);
    }

    /**
     * PUT /transport/private/vehicles/{id}
     */
    public function updateVehicle(Request $request, $id)
    {
        $vehicle = VehicleType::find($id);

        if (!$vehicle) {
            return $this->notFound('Tipe kendaraan tidak ditemukan');
        }

        $validator = Validator::make($request->all(), [
            'name'               => 'sometimes|string|max:255',
            'default_efficiency' => 'sometimes|numeric|min:0.1',
        ]);

        if ($validator->fails()) {
            return $this->validation($validator->errors());
        }

        $vehicle->update($request->only(['name', 'default_efficiency']));

        return $this->success($vehicle, 'Tipe kendaraan berhasil diperbarui');
    }

    /**
     * DELETE /transport/private/vehicles/{id}
     */
    public function destroyVehicle($id)
    {
        $vehicle = VehicleType::find($id);

        if (!$vehicle) {
            return $this->notFound('Tipe kendaraan tidak ditemukan');
        }

        $vehicle->delete();

        return $this->success(null, 'Tipe kendaraan berhasil dihapus');
    }

    /* --------------------------------------------------------------------------
     | FUEL TYPES
     -------------------------------------------------------------------------- */

    /**
     * GET /transport/private/fuels
     */
    public function fuels()
    {
        return $this->success(FuelType::all(), 'Daftar jenis bahan bakar berhasil diambil');
    }

    /**
     * POST /transport/private/fuels
     */
    public function storeFuel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'            => 'required|string|max:255',
            'emission_factor' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return $this->validation($validator->errors());
        }

        $fuel = FuelType::create($request->only(['name', 'emission_factor']));

        return $this->success($fuel, 'Jenis bahan bakar berhasil ditambahkan', 201);
    }

    /**
     * PUT /transport/private/fuels/{id}
     */
    public function updateFuel(Request $request, $id)
    {
        $fuel = FuelType::find($id);

        if (!$fuel) {
            return $this->notFound('Jenis bahan bakar tidak ditemukan');
        }

        $validator = Validator::make($request->all(), [
            'name'            => 'sometimes|string|max:255',
            'emission_factor' => 'sometimes|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return $this->validation($validator->errors());
        }

        $fuel->update($request->only(['name', 'emission_factor']));

        return $this->success($fuel, 'Jenis bahan bakar berhasil diperbarui');
    }

    /**
     * DELETE /transport/private/fuels/{id}
     */
    public function destroyFuel($id)
    {
        $fuel = FuelType::find($id);

        if (!$fuel) {
            return $this->notFound('Jenis bahan bakar tidak ditemukan');
        }

        $fuel->delete();

        return $this->success(null, 'Jenis bahan bakar berhasil dihapus');
    }
}
