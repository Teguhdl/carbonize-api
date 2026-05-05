<?php

namespace App\Http\Controllers\FoodPackaging;

use App\Http\Controllers\BaseController;
use App\Models\ConsumptionEntry;
use App\Services\Emission\EmissionCalculatorFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FoodConsumptionController extends BaseController
{
    /**
     * POST /food-packaging/entries
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'food_item_id' => 'required|integer|exists:food_items,id',
            'quantity'     => 'required|numeric|min:0.001',
            'entry_date'   => 'required|date',
            'image'        => 'nullable|file|image|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->validation($validator->errors());
        }

        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('consumption_images', 'public')
            : null;

        $calculator = EmissionCalculatorFactory::make('food');
        $emissions  = $calculator->calculate($request->all());

        $entry = ConsumptionEntry::create([
            'user_id'      => $request->custom_user_id,
            'entry_type'   => 'food',
            'food_item_id' => $request->food_item_id,
            'entry_date'   => $request->entry_date,
            'quantity'     => $request->quantity,
            'emissions'    => $emissions,
            'image'        => $imagePath,
            'metadata'     => $request->metadata ?? [],
        ]);

        return $this->success(
            $entry->load('foodItem'),
            'Entri konsumsi makanan berhasil dicatat',
            201
        );
    }
}
