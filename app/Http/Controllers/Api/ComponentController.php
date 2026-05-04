<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Component;
use App\Models\Element;
use App\Models\Form;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ComponentController extends Controller
{
    /**
     * List all components with their form and elements.
     * TODO: uncomment the where clause below once created_by is added to the components table.
     */
    public function index(Request $request)
    {
        $components = Component::with(['form', 'elements' => function ($q) {
                $q->withPivot('amount', 'element_unit_id');
            }])
            // ->where('created_by', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $components,
        ]);
    }

    /**
     * Show a single component with its elements.
     */
    public function show(Component $component)
    {
        $component->load(['form', 'elements' => function ($q) {
            $q->withPivot('amount', 'element_unit_id');
        }]);

        return response()->json([
            'success' => true,
            'data'    => $component,
        ]);
    }

    /**
     * Create a new component with elements.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code'                       => 'required|string|max:255|unique:components,code',
            'name'                       => 'required|string|max:255|unique:components,name',
            'description'                => 'nullable|string|max:1000',
            'form'                       => 'required|exists:forms,id',
            'type'                       => 'required|in:1,2,3',
            'elements'                   => 'required|array|min:1',
            'elements.*.element_id'      => 'required|exists:elements,id',
            'elements.*.amount'          => 'required|numeric|min:0|max:99999999.99',
            'elements.*.element_unit_id' => 'required|exists:units,id',
            'attachment'                 => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif,webp',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Check for duplicate element IDs in the payload
        $elementIds = collect($request->elements)->pluck('element_id');
        if ($elementIds->count() !== $elementIds->unique()->count()) {
            return response()->json([
                'success' => false,
                'message' => 'Each element can only be selected once per component.',
            ], 422);
        }

        DB::beginTransaction();

        try {
            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $file     = $request->file('attachment');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('attachments'), $filename);
                $attachmentPath = 'attachments/' . $filename;
            }

            $component = Component::create([
                'code'        => $request->code,
                'name'        => $request->name,
                'description' => $request->description,
                'form_id'     => $request->form,
                'type'        => $request->type,
                'attachment'  => $attachmentPath,
                // 'created_by' => $request->user()->id,
            ]);

            $syncData = [];
            foreach ($request->elements as $element) {
                if (!empty($element['element_id'])) {
                    $syncData[$element['element_id']] = [
                        'amount'          => (float) ($element['amount'] ?? 0),
                        'element_unit_id' => $element['element_unit_id'],
                    ];
                }
            }
            $component->elements()->sync($syncData);

            DB::commit();

            $component->load(['form', 'elements' => function ($q) {
                $q->withPivot('amount', 'element_unit_id');
            }]);

            return response()->json([
                'success' => true,
                'message' => 'Component created successfully.',
                'data'    => $component,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create component: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update a component and its elements.
     */
    public function update(Request $request, Component $component)
    {
        $validator = Validator::make($request->all(), [
            'code'                       => ['required', 'string', 'max:255', Rule::unique('components', 'code')->ignore($component->id)],
            'name'                       => ['required', 'string', 'max:255', Rule::unique('components', 'name')->ignore($component->id)],
            'description'                => 'nullable|string|max:1000',
            'form'                       => 'required|exists:forms,id',
            'type'                       => 'required|in:1,2,3',
            'elements'                   => 'sometimes|array|min:1',
            'elements.*.element_id'      => 'required|exists:elements,id',
            'elements.*.amount'          => 'required|numeric|min:0|max:99999999.99',
            'elements.*.element_unit_id' => 'required|exists:units,id',
            'attachment'                 => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif,webp',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        if ($request->has('elements')) {
            $elementIds = collect($request->elements)->pluck('element_id');
            if ($elementIds->count() !== $elementIds->unique()->count()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Each element can only be selected once per component.',
                ], 422);
            }
        }

        DB::beginTransaction();

        try {
            $updateData = [
                'code'        => $request->code,
                'name'        => $request->name,
                'description' => $request->description,
                'form_id'     => $request->form,
                'type'        => $request->type,
            ];

            if ($request->hasFile('attachment')) {
                if ($component->attachment && file_exists(public_path($component->attachment))) {
                    unlink(public_path($component->attachment));
                }
                $file     = $request->file('attachment');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('attachments'), $filename);
                $updateData['attachment'] = 'attachments/' . $filename;
            }

            $component->update($updateData);

            if ($request->has('elements')) {
                $syncData = [];
                foreach ($request->elements as $element) {
                    if (!empty($element['element_id'])) {
                        $syncData[$element['element_id']] = [
                            'amount'          => (float) ($element['amount'] ?? 0),
                            'element_unit_id' => $element['element_unit_id'],
                        ];
                    }
                }
                $component->elements()->sync($syncData);
            }

            DB::commit();

            $component->load(['form', 'elements' => function ($q) {
                $q->withPivot('amount', 'element_unit_id');
            }]);

            return response()->json([
                'success' => true,
                'message' => 'Component updated successfully.',
                'data'    => $component,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update component: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a component and detach its elements.
     */
    public function destroy(Component $component)
    {
        try {
            $component->elements()->detach();

            if ($component->attachment && file_exists(public_path($component->attachment))) {
                unlink(public_path($component->attachment));
            }

            $component->delete();

            return response()->json([
                'success' => true,
                'message' => 'Component deleted successfully.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete component.',
            ], 500);
        }
    }

    /**
     * Get units available for a given form.
     */
    public function getUnitsByForm(Form $form)
    {
        return response()->json([
            'success' => true,
            'data'    => $form->units,
        ]);
    }
}
