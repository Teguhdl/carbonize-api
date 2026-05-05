<?php

namespace App\Http\Controllers\Transport;

use App\Http\Controllers\BaseController;
use App\Models\TransitVehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PublicTransitController extends BaseController
{
    /**
     * GET /transport/public/vehicles
     */
    public function index()
    {
        return $this->success(TransitVehicle::all(), 'Daftar kendaraan umum berhasil diambil');
    }

    /**
     * GET /transport/public/vehicles/{id}
     */
    public function show($id)
    {
        $vehicle = TransitVehicle::find($id);

        if (!$vehicle) {
            return $this->notFound('Kendaraan umum tidak ditemukan');
        }

        return $this->success($vehicle, 'Kendaraan umum berhasil diambil');
    }

    /**
     * POST /transport/public/vehicles
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'            => 'required|string|max:255',
            'emission_factor' => 'required|numeric|min:0',
            'avg_passengers'  => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return $this->validation($validator->errors());
        }

        $vehicle = TransitVehicle::create($request->only([
            'name', 'emission_factor', 'avg_passengers',
        ]));

        return $this->success($vehicle, 'Kendaraan umum berhasil ditambahkan', 201);
    }

    /**
     * PUT /transport/public/vehicles/{id}
     */
    public function update(Request $request, $id)
    {
        $vehicle = TransitVehicle::find($id);

        if (!$vehicle) {
            return $this->notFound('Kendaraan umum tidak ditemukan');
        }

        $validator = Validator::make($request->all(), [
            'name'            => 'sometimes|string|max:255',
            'emission_factor' => 'sometimes|numeric|min:0',
            'avg_passengers'  => 'sometimes|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return $this->validation($validator->errors());
        }

        $vehicle->update($request->only([
            'name', 'emission_factor', 'avg_passengers',
        ]));

        return $this->success($vehicle, 'Kendaraan umum berhasil diperbarui');
    }

    /**
     * DELETE /transport/public/vehicles/{id}
     */
    public function destroy($id)
    {
        $vehicle = TransitVehicle::find($id);

        if (!$vehicle) {
            return $this->notFound('Kendaraan umum tidak ditemukan');
        }

        $vehicle->delete();

        return $this->success(null, 'Kendaraan umum berhasil dihapus');
    }
}
