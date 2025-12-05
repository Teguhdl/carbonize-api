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
            'user_id'          => 'required|integer|exists:users,id',
            'factor_items_id'  => 'required|integer|exists:emission_factor_items,id',
            'quantity'         => 'required|numeric|min:0.1',
            'entry_date'       => 'required|date',
            'image'            => 'nullable|string',
            'metadata'         => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return $this->validation($validator->errors());
        }

        $user = User::find($request->user_id);
        $factor = EmissionFactorItem::find($request->factor_items_id);

        if (!$user || !$factor) {
            return $this->notFound('User atau faktor emisi tidak ditemukan');
        }

        $emissions = $request->quantity * floatval($factor->value);

        $entry = ConsumptionEntry::create([
            'user_id'         => $request->user_id,
            'factor_items_id' => $request->factor_items_id,
            'entry_date'      => $request->entry_date,
            'quantity'        => $request->quantity,
            'emissions'       => $emissions,
            'image'           => $request->image,
            'metadata'        => $request->metadata,
            'createdAt'       => now()
        ]);

        return $this->success(
            $entry,
            'Consumption entry created successfully',
            201
        );
    }

    public function index(Request $request)
    {
        if (!$request->has('user_id')) {
            return $this->error('Parameter user_id wajib diisi', 400);
        }

        $entries = ConsumptionEntry::where('user_id', $request->user_id)
            ->orderBy('entry_date', 'desc')
            ->get();

        if ($entries->isEmpty()) {
            return $this->notFound('Tidak ada data konsumsi untuk user ini');
        }

        return $this->success(
            $entries,
            'Consumption entries retrieved successfully'
        );
    }

    public function show($id)
    {
        $entry = ConsumptionEntry::find($id);

        if (!$entry) {
            return $this->notFound('Consumption entry not found');
        }

        return $this->success($entry);
    }

    public function destroy($id)
    {
        $entry = ConsumptionEntry::find($id);

        if (!$entry) {
            return $this->notFound('Consumption entry not found');
        }

        $entry->delete();

        return $this->success(
            null,
            'Consumption entry deleted successfully'
        );
    }
}
