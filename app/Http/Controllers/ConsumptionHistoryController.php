<?php

namespace App\Http\Controllers;

use App\Models\ConsumptionEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConsumptionHistoryController extends BaseController
{
    /**
     * GET /entries
     *
     * Riwayat seluruh konsumsi milik user yang sedang login.
     * Filter opsional: ?entry_type=food|private_vehicle|public_transit
     *                  ?start_date=YYYY-MM-DD
     *                  ?end_date=YYYY-MM-DD
     */
    public function index(Request $request)
    {
        $query = ConsumptionEntry::with([
            'foodItem',
            'vehicleType',
            'fuelType',
            'transitVehicle',
        ])->where('user_id', $request->custom_user_id);

        if ($request->filled('entry_type')) {
            $query->where('entry_type', $request->entry_type);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('entry_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('entry_date', '<=', $request->end_date);
        }

        if ($request->filled('date')) {
            $query->whereDate('entry_date', $request->date);
        }

        $entries = $query->orderBy('entry_date', 'desc')->get();

        return $this->success($entries, 'Riwayat konsumsi berhasil diambil');
    }

    /**
     * GET /entries/{id}
     */
    public function show($id)
    {
        $entry = ConsumptionEntry::with([
            'foodItem',
            'vehicleType',
            'fuelType',
            'transitVehicle',
        ])->find($id);

        if (!$entry) {
            return $this->notFound('Entri konsumsi tidak ditemukan');
        }

        return $this->success($entry, 'Entri berhasil diambil');
    }

    /**
     * DELETE /entries/{id}
     */
    public function destroy($id)
    {
        $entry = ConsumptionEntry::find($id);

        if (!$entry) {
            return $this->notFound('Entri konsumsi tidak ditemukan');
        }

        $entry->delete();

        return $this->success(null, 'Entri konsumsi berhasil dihapus');
    }
}
