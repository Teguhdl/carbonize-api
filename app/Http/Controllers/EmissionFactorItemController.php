<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmissionFactorItem;
use Illuminate\Support\Facades\Validator;

class EmissionFactorItemController extends BaseController
{
 
    public function index(Request $request)
    {
        $query = EmissionFactorItem::query();

        if ($request->has('category_id')) {
            $query->where('factor_category_id', $request->category_id);
        }

        $items = $query->get();

        if ($items->isEmpty()) {
            return $this->notFound('Item faktor emisi tidak ditemukan');
        }

        return $this->success(
            $items,
            'Item faktor emisi berhasil diambil'
        );
    }

  
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'factor_category_id' => 'required|integer|exists:emission_factor_categories,id',
            'name'               => 'required|string|max:255',
            'value'              => 'required', 
        ]);

        if ($validator->fails()) {
            return $this->validation($validator->errors());
        }

        $item = EmissionFactorItem::create([
            'factor_category_id' => $request->factor_category_id,
            'name'               => $request->name,
            'value'              => $request->value,
        ]);

        return $this->success(
            $item,
            'Item faktor emisi berhasil dibuat',
            201
        );
    }

   
    public function show($id)
    {
        $item = EmissionFactorItem::find($id);

        if (!$item) {
            return $this->notFound('Item faktor emisi tidak ditemukan');
        }

        return $this->success(
            $item,
            'Item faktor emisi berhasil diambil'
        );
    }

    
    public function update(Request $request, $id)
    {
        $item = EmissionFactorItem::find($id);

        if (!$item) {
            return $this->notFound('Item faktor emisi tidak ditemukan');
        }

        $validator = Validator::make($request->all(), [
            'factor_category_id' => 'nullable|integer|exists:emission_factor_categories,id',
            'name'               => 'nullable|string|max:255',
            'value'              => 'nullable',
        ]);

        if ($validator->fails()) {
            return $this->validation($validator->errors());
        }

        $item->update($request->only([
            'factor_category_id',
            'name',
            'value',
        ]));

        return $this->success(
            $item,
            'Item faktor emisi berhasil diperbarui'
        );
    }

   
    public function destroy($id)
    {
        $item = EmissionFactorItem::find($id);

        if (!$item) {
            return $this->notFound('Item faktor emisi tidak ditemukan');
        }

        $item->delete();

        return $this->success(
            null,
            'Item faktor emisi berhasil dihapus'
        );
    }
}
