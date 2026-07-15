<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CompoPrice;
use App\Constants\Unit;
use Illuminate\Http\Request;

class CompoPriceController extends Controller
{
    /**
     * List all compo prices with their component details.
     */
    public function index(Request $request)
    {
        $compoPrices = CompoPrice::with('component')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'component_id' => $item->component_id,
                    'component_code' => $item->component->code ?? null,
                    'component_name' => $item->component->name ?? null,
                    'price' => (float) $item->price,
                    'unit' => $item->unit,
                    'unit_label' => Unit::getUnit()[$item->unit] ?? null,
                    'pricing_date' => $item->pricing_date,
                    'set_last_date' => $item->set_last_date,
                    'set_last_unit' => $item->set_last_unit,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => $compoPrices,
        ]);
    }

    /**
     * Show a single compo price.
     */
    public function show(CompoPrice $compoPrice)
    {
        $compoPrice->load('component');

        $data = [
            'id' => $compoPrice->id,
            'component_id' => $compoPrice->component_id,
            'component_code' => $compoPrice->component->code ?? null,
            'component_name' => $compoPrice->component->name ?? null,
            'price' => (float) $compoPrice->price,
            'unit' => $compoPrice->unit,
            'unit_label' => Unit::getUnit()[$compoPrice->unit] ?? null,
            'pricing_date' => $compoPrice->pricing_date,
            'set_last_date' => $compoPrice->set_last_date,
            'set_last_unit' => $compoPrice->set_last_unit,
            'created_at' => $compoPrice->created_at,
            'updated_at' => $compoPrice->updated_at,
        ];

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }

    /**
     * Get compo prices by component.
     */
    public function getByComponent(Request $request)
    {
        $componentId = $request->input('component_id');

        if (!$componentId) {
            return response()->json([
                'success' => false,
                'message' => 'component_id is required.',
            ], 400);
        }

        $compoPrices = CompoPrice::where('component_id', $componentId)
            ->with('component')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'component_id' => $item->component_id,
                    'component_code' => $item->component->code ?? null,
                    'component_name' => $item->component->name ?? null,
                    'price' => (float) $item->price,
                    'unit' => $item->unit,
                    'unit_label' => Unit::getUnit()[$item->unit] ?? null,
                    'pricing_date' => $item->pricing_date,
                    'set_last_date' => $item->set_last_date,
                    'set_last_unit' => $item->set_last_unit,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => $compoPrices,
        ]);
    }

    /**
     * Get latest compo price for a component.
     */
    public function getLatestByComponent(Request $request)
    {
        $componentId = $request->input('component_id');

        if (!$componentId) {
            return response()->json([
                'success' => false,
                'message' => 'component_id is required.',
            ], 400);
        }

        $compoPrice = CompoPrice::where('component_id', $componentId)
            ->with('component')
            ->orderBy('pricing_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$compoPrice) {
            return response()->json([
                'success' => false,
                'message' => 'No compo price found for this component.',
            ], 404);
        }

        $data = [
            'id' => $compoPrice->id,
            'component_id' => $compoPrice->component_id,
            'component_code' => $compoPrice->component->code ?? null,
            'component_name' => $compoPrice->component->name ?? null,
            'price' => (float) $compoPrice->price,
            'unit' => $compoPrice->unit,
            'unit_label' => Unit::getUnit()[$compoPrice->unit] ?? null,
            'pricing_date' => $compoPrice->pricing_date,
            'set_last_date' => $compoPrice->set_last_date,
            'set_last_unit' => $compoPrice->set_last_unit,
            'created_at' => $compoPrice->created_at,
            'updated_at' => $compoPrice->updated_at,
        ];

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }
}
