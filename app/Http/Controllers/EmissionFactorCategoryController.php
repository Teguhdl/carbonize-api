<?php

namespace App\Http\Controllers;

use App\Models\EmissionFactorCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmissionFactorCategoryController extends BaseController
{

    public function index()
    {
        $categories = EmissionFactorCategory::with('items')->get();

        return $this->success(
            $categories,
            'Emission factor categories retrieved successfully'
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
            'Emission factor category created successfully',
            201
        );
    }


    public function show($id)
    {
        $category = EmissionFactorCategory::with('items')->find($id);

        if (!$category) {
            return $this->notFound('Emission factor category not found');
        }

        return $this->success(
            $category,
            'Emission factor category retrieved successfully'
        );
    }

   
    public function update(Request $request, $id)
    {
        $category = EmissionFactorCategory::find($id);

        if (!$category) {
            return $this->notFound('Emission factor category not found');
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
            'Emission factor category updated successfully'
        );
    }

   
    public function destroy($id)
    {
        $category = EmissionFactorCategory::find($id);

        if (!$category) {
            return $this->notFound('Emission factor category not found');
        }

        $category->delete();

        return $this->success(
            null,
            'Emission factor category deleted successfully'
        );
    }
}
