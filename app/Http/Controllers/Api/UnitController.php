<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnitController extends Controller
{
    /**
     * List all units.
     * TODO: uncomment the where clause below once created_by is added to the units table.
     */
    public function index(Request $request)
    {
        $units = Unit::query()
            // ->where('created_by', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $units,
        ]);
    }

    /**
     * Show a single unit.
     */
    public function show(Unit $unit)
    {
        return response()->json([
            'success' => true,
            'data'    => $unit,
        ]);
    }

    /**
     * Create a new unit.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'   => 'required|string|max:255',
            'symbol' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $unit = Unit::create([
            'name'   => $request->name,
            'symbol' => $request->symbol,
            // 'created_by' => $request->user()->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Unit created successfully.',
            'data'    => $unit,
        ], 201);
    }

    /**
     * Update a unit.
     */
    public function update(Request $request, Unit $unit)
    {
        $validator = Validator::make($request->all(), [
            'name'   => 'required|string|max:255',
            'symbol' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $unit->update([
            'name'   => $request->name,
            'symbol' => $request->symbol,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Unit updated successfully.',
            'data'    => $unit->fresh(),
        ]);
    }

    /**
     * Delete a unit.
     */
    public function destroy(Unit $unit)
    {
        $unit->delete();

        return response()->json([
            'success' => true,
            'message' => 'Unit deleted successfully.',
        ]);
    }
}
