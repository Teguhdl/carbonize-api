<?php

namespace App\Http\Controllers\FoodPackaging;

use App\Http\Controllers\BaseController;
use App\Models\FoodItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FoodItemController extends BaseController
{
    /**
     * GET /food-packaging/items
     * GET /food-packaging/items?method=fixed
     * GET /food-packaging/items?method=climatiq
     */
    public function index(Request $request)
    {
        $query = FoodItem::query();

        if ($request->filled('method')) {
            $query->where('calculation_method', $request->method);
        }

        return $this->success($query->get(), 'Daftar item makanan & packaging berhasil diambil');
    }

    /**
     * GET /food-packaging/items/{id}
     */
    public function show($id)
    {
        $item = FoodItem::find($id);

        if (!$item) {
            return $this->notFound('Item tidak ditemukan');
        }

        return $this->success($item, 'Item berhasil diambil');
    }

    /**
     * POST /food-packaging/items
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'               => 'required|string|max:255',
            'calculation_method' => 'required|in:fixed,climatiq',
            'emission_factor'    => 'required_if:calculation_method,fixed|nullable|numeric|min:0',
            'climatiq_id'        => 'required_if:calculation_method,climatiq|nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validation($validator->errors());
        }

        $item = FoodItem::create($request->only([
            'name', 'calculation_method', 'emission_factor', 'climatiq_id',
        ]));

        return $this->success($item, 'Item berhasil ditambahkan', 201);
    }

    /**
     * PUT /food-packaging/items/{id}
     */
    public function update(Request $request, $id)
    {
        $item = FoodItem::find($id);

        if (!$item) {
            return $this->notFound('Item tidak ditemukan');
        }

        $validator = Validator::make($request->all(), [
            'name'               => 'sometimes|string|max:255',
            'calculation_method' => 'sometimes|in:fixed,climatiq',
            'emission_factor'    => 'nullable|numeric|min:0',
            'climatiq_id'        => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validation($validator->errors());
        }

        $item->update($request->only([
            'name', 'calculation_method', 'emission_factor', 'climatiq_id',
        ]));

        return $this->success($item, 'Item berhasil diperbarui');
    }

    /**
     * DELETE /food-packaging/items/{id}
     */
    public function destroy($id)
    {
        $item = FoodItem::find($id);

        if (!$item) {
            return $this->notFound('Item tidak ditemukan');
        }

        $item->delete();

        return $this->success(null, 'Item berhasil dihapus');
    }
}
