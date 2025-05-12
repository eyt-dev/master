<?php

namespace App\Http\Controllers;

use App\Constants\NutritionType;
use App\Http\Requests\StoreComponentRequest;
use App\Http\Requests\UpdateComponentRequest;
use App\Models\Component;
use App\Models\Element;
use App\Models\Form;
use App\Models\Unit;
use Illuminate\Support\Facades\Log;
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
                    return $row->form?->unit->name;
                })
                ->addColumn('action', function ($row) {
                    return '<a class="edit-component btn btn-sm btn-success" data-path="' . route('component.edit', $row->id) . '" title="Edit" style="margin-right: 5px;">
                    <i class="fa fa-edit"></i></a>'
                        . '<a class="delete-component btn btn-sm btn-danger" data-id="' . $row->id . '" title="Delete" style="margin-right: 5px;">
                    <i class="fa fa-trash"></i></a>';
                })
                ->addIndexColumn()
                ->rawColumns(['action','form','type'])
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
        return view('component.create', ['component' => null, 'forms' => Form::all(), 'units' => Unit::all(), 'elements' => Element::all()
        ,'elementsUnit' => Unit::all()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreComponentRequest $request)
    {
        $data = $request->validated();

        $elements = collect($data['elements'])
            ->reject(function ($item, $key) {
                return $key === '__index__';
            });

        $component = Component::create([
            'code' => $data['code'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'form_id' => $data['form'],
            'unit_id' => $data['unit'],
            'type' => $data['type'],
        ]);

        $syncData = [];

        foreach ($elements as $element) {
            if (isset($element['element_id'])) {
                $syncData[$element['element_id']] = ['amount' => $element['amount'] ?? 1];
            }
        }

        $component->elements()->sync($syncData);

        Session::flash('successMsg', 'Component created successfully.');

        return redirect()->route('component.index');
    }

    public function edit(Component $component)
    {
        // Load necessary relationships
        $component->load('elements');

        $forms = Form::all();
        $units = Unit::all();
        $elements = Element::all();

        $componentElementsJson = $component->elements->map(function($element) {
            return [
                'id' => $element->id,
                'pivot' => [
                    'amount' => $element->pivot->amount
                ]
            ];
        });

        return view('component.create', compact('component', 'forms', 'units', 'elements', 'componentElementsJson'));
    }

    public function update(UpdateComponentRequest $request, Component $component)
    {
        $data = $request->validated();

        $elements = collect($data['elements'] ?? [])
            ->reject(function ($item, $key) {
                return $key === '__index__';
            });

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
                $syncData[$element['element_id']] = ['amount' => $element['amount'] ?? 1];
            }
        }

        $component->elements()->sync($syncData);

        Session::flash('successMsg', 'Component updated successfully.');
        return redirect()->route('component.index');
    }

    public function getUnitByForm($formId)
    {
        $form = Form::firstWhere('id', $formId);

        $unit = Unit::firstWhere('id', $form->unit_id);

        return response()->json($unit);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Component::findOrFail($id)->delete();

        return response()->json(['msg' => 'Component deleted successfully.']);
    }
}
