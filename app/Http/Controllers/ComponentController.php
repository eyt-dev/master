<?php

namespace App\Http\Controllers;

use App\Constants\NutritionType;
use App\Http\Requests\StoreComponentRequest;
use App\Http\Requests\UpdateComponentRequest;
use App\Models\Component;
use App\Models\Element;
use App\Models\Form;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ComponentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $data = Component::when(auth()->user()->role !== 'SuperAdmin', function ($query) {
                $query->where('created_by', auth()->id());
            })
                ->orderBy('created_at', 'desc')->get();
            return datatables()->of($data)
                ->addColumn('form', function ($row) {
                    return $row->form?->name ?? '<span class="text-muted">No Form</span>';
                })
                ->addColumn('type', function ($row) {
                    $badge = NutritionType::getNutritionbadge()[$row->type] ?? 'secondary';
                    $label = NutritionType::getNutritionType()[$row->type] ?? 'unknown';
                    return '<span class="text-white badge bg-' . $badge . '">' . $label . '</span>';
                })
                ->addColumn('unit', function ($row) {
                    return $row->unit->symbol;
                })
                ->addColumn('action', function ($row) {
                    return '<a class="edit-component btn btn-sm btn-success" data-path="' . route('component.edit', $row->id) . '" title="Edit" style="margin-right: 5px;">
                    <i class="fa fa-edit"></i></a>'
                        . '<a class="delete-component btn btn-sm btn-danger" data-id="' . $row->id . '" title="Delete" style="margin-right: 5px;">
                    <i class="fa fa-trash"></i></a>';
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'form', 'type', 'unit', 'description'])
                ->make(true);
        }

        $components = Component::orderBy('id', 'desc')->paginate(10);

        return view('component.index', compact('components'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('component.create', [
            'component' => null,
            'forms' => Form::all(),
            'units' => Unit::all(),
            'elements' => Element::all(), // Show all elements, we'll handle selection in JS
            'elementsUnit' => Unit::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreComponentRequest $request)
    {
        $data = $request->validated();

        // Clean elements data (remove template placeholder)
        $elements = collect($data['elements'])
            ->reject(function ($item, $key) {
                return $key === '__index__' || empty($item['element_id']);
            });

        // Check for duplicate elements
        $elementIds = $elements->pluck('element_id');
        if ($elementIds->count() !== $elementIds->unique()->count()) {
            return back()->withErrors(['elements' => 'Each element can only be selected once per component.'])->withInput();
        }

        DB::beginTransaction();

        try {
            $component = Component::create([
                'code' => $data['code'],
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'form_id' => $data['form'],
                'unit_id' => $data['unit'],
                'type' => $data['type'],
                'created_by' => auth()->id(),
            ]);

            $syncData = [];

            foreach ($elements as $element) {
                if (isset($element['element_id']) && $element['element_id']) {
                    $syncData[$element['element_id']] = [
                        'amount' => $element['amount'],
                        'element_unit_id' => $element['element_unit_id']
                    ];
                }
            }

            $component->elements()->sync($syncData);

            DB::commit();

            Session::flash('successMsg', 'Component created successfully.');
            return redirect()->route('component.index');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to create component. Please try again.'])->withInput();
        }
    }


    public function edit(Component $component)
    {
        // Load the component with its elements and pivot data
        $component->load(['elements' => function ($query) {
            $query->withPivot('amount', 'element_unit_id');
        }]);

        // Get other required data
        $forms = Form::all();
        $units = Unit::all();
        $elements = Element::all();

        // Convert elements to JSON for JavaScript
        $componentElementsJson = $component->elements->toJson();

        return view('component.create', compact(
            'component',
            'forms',
            'units',
            'elements',
            'componentElementsJson'
        ));
    }

    public function update(UpdateComponentRequest $request, Component $component)
    {
        $data = $request->validated();

        // Clean elements data
        $elements = collect($data['elements'] ?? [])
            ->reject(function ($item, $key) {
                return $key === '__index__' || empty($item['element_id']);
            });

        // Check for duplicate elements
        $elementIds = $elements->pluck('element_id');
        if ($elementIds->count() !== $elementIds->unique()->count()) {
            return back()->withErrors(['elements' => 'Each element can only be selected once per component.'])->withInput();
        }

        DB::beginTransaction();

        try {
            $component->update([
                'code' => $data['code'],
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'form_id' => $data['form'],
                'unit_id' => $data['unit'],
                'type' => $data['type'],
            ]);

            $syncData = [];
            foreach ($elements as $element) {
                if (isset($element['element_id']) && $element['element_id']) {
                    $syncData[$element['element_id']] = [
                        'amount' => $element['amount'] ?? 1,
                        'element_unit_id' => $element['element_unit_id']
                    ];
                }
            }

            $component->elements()->sync($syncData);

            DB::commit();

            Session::flash('successMsg', 'Component updated successfully.');
            return redirect()->route('component.index');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to update component. Please try again.'])->withInput();
        }
    }

    public function getUnitByForm($formId)
    {
        try {
            $form = Form::findOrFail($formId);
            $units = $form->units;
            return response()->json($units);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch units'], 500);
        }
    }

    public function checkCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:255',
            'id' => 'nullable|integer|exists:components,id'
        ]);

        $code = $request->input('code');
        $componentId = $request->input('id');

        // Build query to check if code exists
        $query = Component::where('code', $code);

        // If we're updating an existing component, exclude it from the check
        if ($componentId) {
            $query->where('id', '!=', $componentId);
        }

        $exists = $query->exists();

        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'This code is already taken by another component.' : 'Code is available.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $component = Component::findOrFail($id);
            $component->elements()->detach(); // Remove element relationships
            $component->delete();

            return response()->json(['msg' => 'Component deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete component.'], 500);
        }
    }
}
