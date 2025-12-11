<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConsumptionEntry;
use App\Models\User;
use App\Models\EmissionFactorItem;
use Illuminate\Support\Facades\Validator;

class ConsumptionEntryController extends BaseController
{
  
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
        ];

        $requestMetadata = $request->input('metadata', []);
        if (!is_array($requestMetadata)) {
            $requestMetadata = [];
        }
        $metadata = array_merge($defaultMetadata, $requestMetadata);

        $emissions = $request->quantity * floatval($factor->value);

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

        return $this->success(
            $entry,
            'Entri konsumsi berhasil dibuat',
            201
        );
    }

    public function index(Request $request)
    {
        $userId = $request->custom_user_id;

        $entries = ConsumptionEntry::where('user_id', $userId)
            ->orderBy('entry_date', 'desc')
            ->get();

        if ($entries->isEmpty()) {
            return $this->notFound('Tidak ada data konsumsi untuk user ini');
        }

        return $this->success(
            $entries,
            'Data konsumsi berhasil diambil'
        );
    }

    public function show($id)
    {
        $entry = ConsumptionEntry::find($id);

        if (!$entry) {
            return $this->notFound('Entri konsumsi tidak ditemukan');
        }

        return $this->success($entry);
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
