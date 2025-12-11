<?php

namespace App\Http\Controllers;

use App\Models\EmissionFactorCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmissionFactorCategoryController extends BaseController
{

    public function index(Request $request)
    {
        $query = EmissionFactorCategory::query();

        if ($request->input('iswithItems', '0') == '1') {
            $query->with('items');
        }

        $categories = $query->get();

        return $this->success(
            $categories,
            'Kategori faktor emisi berhasil diambil'
        );
    }

    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->validation($validator->errors());
        }

        $category = EmissionFactorCategory::create([
            'category_name' => $request->category_name,
        ]);

        return $this->success(
            $category,
            'Kategori faktor emisi berhasil dibuat',
            201
        );
    }


    public function show($id)
    {
        $category = EmissionFactorCategory::with('items')->find($id);

        if (!$category) {
            return $this->notFound('Kategori faktor emisi tidak ditemukan');
        }

        return $this->success(
            $category,
            'Kategori faktor emisi berhasil diambil'
        );
    }

   
    public function update(Request $request, $id)
    {
        $category = EmissionFactorCategory::find($id);

        if (!$category) {
            return $this->notFound('Kategori faktor emisi tidak ditemukan');
        }

        $validator = Validator::make($request->all(), [
            'category_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->validation($validator->errors());
        }

        $category->category_name = $request->category_name;
        $category->save();

        return $this->success(
            $category,
            'Kategori faktor emisi berhasil diperbarui'
        );
    }

   
    public function destroy($id)
    {
        $category = EmissionFactorCategory::find($id);

        if (!$category) {
            return $this->notFound('Kategori faktor emisi tidak ditemukan');
        }

        $category->delete();

        return $this->success(
            null,
            'Kategori faktor emisi berhasil dihapus'
        );
    }
}
