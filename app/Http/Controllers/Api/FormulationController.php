<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Formulation;
use App\Models\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FormulationController extends Controller
{
    /**
     * List all formulations with pagination, sorting, and filtering
     */
    public function index(Request $request)
    {
        $formulations = Formulation::with([
                'creator',
                'template',
                'components.component',
                'components.componentType',
                'analysis'
            ])
            ->when($request->search, function ($q) use ($request) {
                return $q->where('formulation_code', 'like', "%{$request->search}%")
                    ->orWhere('name', 'like', "%{$request->search}%")
                    ->orWhere('target_animal', 'like', "%{$request->search}%");
            })
            ->when($request->target_animal, function ($q) use ($request) {
                return $q->where('target_animal', $request->target_animal);
            })
            ->when($request->created_by, function ($q) use ($request) {
                return $q->where('created_by', $request->created_by);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data'    => $formulations,
        ]);
    }

    /**
     * Store a newly created formulation
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'formulation_code' => 'required|string|unique:formulations,formulation_code',
            'name' => 'required|string|max:255',
            'target_animal' => 'required|in:broiler,layer,breeder,swine,cattle',
            'inclusion_percentage' => 'required|numeric|min:0|max:100',
            'total_volume' => 'nullable|integer|min:1',
            'indication_of_use' => 'nullable|string',
            'reference' => 'nullable|string|max:255',
            'template_id' => 'nullable|exists:formulations,id',
            'components' => 'required|array|min:1',
            'components.*.component_id' => 'required|exists:components,id',
            'components.*.component_type_id' => 'required|exists:forms,id',
            'components.*.quantity' => 'required|numeric|min:0.001',
            'components.*.price' => 'required|numeric|min:0',
            'analysis' => 'required|array|min:1',
            'analysis.*.element_name' => 'required|string|max:255',
            'analysis.*.premix_value' => 'nullable|numeric',
            'analysis.*.feed_value' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();

        try {
            $formulation = Formulation::create([
                'formulation_code' => $request->formulation_code,
                'name' => $request->name,
                'target_animal' => $request->target_animal,
                'inclusion_percentage' => $request->inclusion_percentage,
                'total_volume' => $request->total_volume,
                'indication_of_use' => $request->indication_of_use,
                'reference' => $request->reference,
                'template_id' => $request->template_id,
                'created_by' => auth()->id(),
            ]);

            foreach ($request->components as $component) {
                $formulation->components()->create([
                    'component_id' => $component['component_id'],
                    'component_type_id' => $component['component_type_id'],
                    'quantity' => $component['quantity'],
                    'price' => $component['price'],
                ]);
            }

            foreach ($request->analysis as $analysis) {
                $formulation->analysis()->create([
                    'element_name' => $analysis['element_name'],
                    'premix_value' => $analysis['premix_value'] ?? null,
                    'feed_value' => $analysis['feed_value'] ?? null,
                ]);
            }

            DB::commit();

            $formulation->load(['creator', 'template', 'components.component', 'components.componentType', 'analysis']);

            return response()->json([
                'success' => true,
                'message' => 'Formulation created successfully.',
                'data'    => $formulation,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create formulation: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show a single formulation
     */
    public function show(Formulation $formulation)
    {
        $formulation->load(['creator', 'template', 'components.component', 'components.componentType', 'analysis']);

        return response()->json([
            'success' => true,
            'data'    => $formulation,
        ]);
    }

    /**
     * Show formulation for edit
     */
    public function edit(Formulation $formulation)
    {
        $formulation->load(['creator', 'template', 'components.component', 'components.componentType', 'analysis']);

        return response()->json([
            'success' => true,
            'data'    => $formulation,
        ]);
    }

    /**
     * Update a formulation
     */
    public function update(Request $request, Formulation $formulation)
    {
        $validator = Validator::make($request->all(), [
            'formulation_code' => 'required|string|unique:formulations,formulation_code,' . $formulation->id,
            'name' => 'required|string|max:255',
            'target_animal' => 'required|in:broiler,layer,breeder,swine,cattle',
            'inclusion_percentage' => 'required|numeric|min:0|max:100',
            'total_volume' => 'nullable|integer|min:1',
            'indication_of_use' => 'nullable|string',
            'reference' => 'nullable|string|max:255',
            'template_id' => 'nullable|exists:formulations,id',
            'components' => 'required|array|min:1',
            'components.*.component_id' => 'required|exists:components,id',
            'components.*.component_type_id' => 'required|exists:forms,id',
            'components.*.quantity' => 'required|numeric|min:0.001',
            'components.*.price' => 'required|numeric|min:0',
            'analysis' => 'required|array|min:1',
            'analysis.*.element_name' => 'required|string|max:255',
            'analysis.*.premix_value' => 'nullable|numeric',
            'analysis.*.feed_value' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();

        try {
            $formulation->update([
                'formulation_code' => $request->formulation_code,
                'name' => $request->name,
                'target_animal' => $request->target_animal,
                'inclusion_percentage' => $request->inclusion_percentage,
                'total_volume' => $request->total_volume,
                'indication_of_use' => $request->indication_of_use,
                'reference' => $request->reference,
                'template_id' => $request->template_id,
            ]);

            $formulation->components()->delete();
            foreach ($request->components as $component) {
                $formulation->components()->create([
                    'component_id' => $component['component_id'],
                    'component_type_id' => $component['component_type_id'],
                    'quantity' => $component['quantity'],
                    'price' => $component['price'],
                ]);
            }

            $formulation->analysis()->delete();
            foreach ($request->analysis as $analysis) {
                $formulation->analysis()->create([
                    'element_name' => $analysis['element_name'],
                    'premix_value' => $analysis['premix_value'] ?? null,
                    'feed_value' => $analysis['feed_value'] ?? null,
                ]);
            }

            DB::commit();

            $formulation->load(['creator', 'template', 'components.component', 'components.componentType', 'analysis']);

            return response()->json([
                'success' => true,
                'message' => 'Formulation updated successfully.',
                'data'    => $formulation,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update formulation: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a formulation
     */
    public function destroy(Formulation $formulation)
    {
        try {
            $formulation->components()->delete();
            $formulation->analysis()->delete();
            $formulation->delete();

            return response()->json([
                'success' => true,
                'message' => 'Formulation deleted successfully.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete formulation.',
            ], 500);
        }
    }

    /**
     * Get formulation by template ID
     */
    public function getTemplate(Formulation $formulation)
    {
        $formulation->load(['components.component', 'components.componentType', 'analysis']);

        return response()->json([
            'success' => true,
            'data'    => $formulation,
        ]);
    }

    /**
     * Get all component types for dropdown
     */
    public function getComponentTypes()
    {
        $componentTypes = Form::select('id', 'name')->get();

        return response()->json([
            'success' => true,
            'data'    => $componentTypes,
        ]);
    }
}
