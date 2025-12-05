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
            return $this->notFound('Emission factor items not found');
        }

        return $this->success(
            $items,
            'Emission factor items retrieved successfully'
        );
    }

  
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'factor_category_id' => 'required|integer|exists:emission_factor_categories,id',
            'item_name'          => 'required|string|max:255',
            'unit'               => 'required|string|max:50',
            'emission_factor'    => 'required|numeric',
            'metadata'           => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return $this->validation($validator->errors());
        }

        $item = EmissionFactorItem::create([
            'factor_category_id' => $request->factor_category_id,
            'item_name'          => $request->item_name,
            'unit'               => $request->unit,
            'emission_factor'    => $request->emission_factor,
            'metadata'           => $request->metadata
        ]);

        return $this->success(
            $item,
            'Emission factor item created successfully',
            201
        );
    }

   
    public function show($id)
    {
        $item = EmissionFactorItem::find($id);

        if (!$item) {
            return $this->notFound('Emission factor item not found');
        }

        return $this->success(
            $item,
            'Emission factor item retrieved successfully'
        );
    }

    
    public function update(Request $request, $id)
    {
        $item = EmissionFactorItem::find($id);

        if (!$item) {
            return $this->notFound('Emission factor item not found');
        }

        $validator = Validator::make($request->all(), [
            'factor_category_id' => 'nullable|integer|exists:emission_factor_categories,id',
            'item_name'          => 'nullable|string|max:255',
            'unit'               => 'nullable|string|max:50',
            'emission_factor'    => 'nullable|numeric',
            'metadata'           => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return $this->validation($validator->errors());
        }

        $item->update($request->only([
            'factor_category_id',
            'item_name',
            'unit',
            'emission_factor',
            'metadata'
        ]));

        return $this->success(
            $item,
            'Emission factor item updated successfully'
        );
    }

   
    public function destroy($id)
    {
        $item = EmissionFactorItem::find($id);

        if (!$item) {
            return $this->notFound('Emission factor item not found');
        }

        $item->delete();

        return $this->success(
            null,
            'Emission factor item deleted successfully'
        );
    }
}
