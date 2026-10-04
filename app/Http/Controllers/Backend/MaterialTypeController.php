<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\MaterialType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MaterialTypeController extends Controller
{
    public function index($username)
    {
        $types = MaterialType::orderBy('id')->get();
        return response()->json(['success' => true, 'data' => $types]);
    }

    public function store(Request $request, $username)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:material_types',
            'value' => 'required|string|max:255|unique:material_types',
            'hangar_allocation' => 'required|boolean',
        ]);

        $materialType = MaterialType::create($request->only('name', 'value', 'hangar_allocation'));

        return response()->json(['success' => true, 'message' => 'Material type created.', 'data' => $materialType], 201);
    }

    public function update(Request $request, $username, MaterialType $materialType)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:material_types,name,' . $materialType->id,
            'value' => 'required|string|max:255|unique:material_types,value,' . $materialType->id,
            'hangar_allocation' => 'required|boolean',
        ]);

        $materialType->update($request->only('name', 'value', 'hangar_allocation'));

        return response()->json(['success' => true, 'message' => 'Material type updated.', 'data' => $materialType]);
    }

    public function destroy($username, MaterialType $materialType)
    {
        $materialType->delete();
        return response()->json(['success' => true, 'message' => 'Material type deleted.']);
    }
}
