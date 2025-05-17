<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompoPriceRequest;
use App\Http\Requests\UpdateCompoPriceRequest;
use App\Models\Component;
use App\Models\CompoPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CompoPriceController extends Controller
{
    public function getCompoPrices()
    {
        if (request()->ajax()) {
            $data = CompoPrice::when(auth()->user()->role !== 'SuperAdmin', function ($query) {
                $query->where('created_by', auth()->id());
            })
                ->orderBy('created_at', 'desc')->get();
            return datatables()->of($data)
                ->addColumn('code', function ($row) {
                    return $row->component->code;
                })
                ->addColumn('name', function ($row) {
                    return $row->component->name;
                })
                ->addColumn('action', function ($row) {
                    return '<a class="edit-compo-price btn btn-sm btn-success" data-path="' . route('compo_price.edit', $row->id) . '" title="Edit" style="margin-right: 5px;">
                            <i class="fa fa-edit"></i></a>'
                        . '<a class="delete-compo-price btn btn-sm btn-danger" data-id="' . $row->id . '" title="Delete" style="margin-right: 5px;">
                            <i class="fa fa-trash"></i></a>';
                })
                ->addIndexColumn()
                ->rawColumns(['action'])
                ->make(true);
        }

        $compoPrices = CompoPrice::with('component')->get();

        return view('compo_price.index', ['compoPrices' => $compoPrices]);

    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $components = Component::with('elements')->get();

        return view('compo_price.index', ['components' => $components]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompoPriceRequest $request)
    {
        $data = $request->validated();

        list($componentId, $elementId) = explode('_', $request->component);

        $compoPrice = CompoPrice::create([
            'component_id' => $componentId,
            'element_id' => $elementId,
            'pricing_date' => $data['pricing_date'],
            'price' => $data['price'],
            'description' => $data['description'] ?? null,
        ]);

        Session::flash('successMsg', 'Compo Price created successfully.');

        return response()->json(['data' => $compoPrice, 'code' => 200]);
    }

    public function edit(CompoPrice $compoPrice)
    {
        $components = Component::with('elements')->get();

        return view('compo_price.edit', ['compoPrice' => $compoPrice, 'components' => $components]);
    }

    public function update(UpdateCompoPriceRequest $request, CompoPrice $compoPrice)
    {
        $data = $request->validated();

        list($componentId, $elementId) = explode('_', $request->component);

        $compoPrice->update([
            'component_id' => $componentId,
            'element_id' => $elementId,
            'pricing_date' => $data['pricing_date'],
            'price' => $data['price'],
            'description' => $data['description'] ?? null,
        ]);

        Session::flash('successMsg', 'Compo Price updated successfully.');

        return redirect()->route('compo_price.index');
    }

    public function getUnitByForm($formId)
    {
        $form = Form::findOrFail($formId);

        $units = $form->units;

        return response()->json($units);
    }

    public function checkCode(Request $request)
    {
        $code = $request->input('code');
        $id = $request->input('id');

        if ($id) {
            $component = Component::find($id);
            if ($component && $component->code == $code) {
                return response()->json(false);
            }
        }

        $exists = Component::where('code', $code)->exists();

        return response()->json(!$exists);
    }

    public function checkUnique(Request $request)
    {
        $exists = CompoPrice::where('component_id', explode('_', $request->component)[0])
            ->where('element_id', explode('_', $request->component)[1])
            ->where('pricing_date', $request->pricing_date)
            ->when($request->id, function ($q) use ($request) {
                return $q->where('id', '!=', $request->id);
            })
            ->exists();

        return response()->json(!$exists);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        CompoPrice::findOrFail($id)->delete();

        return response()->json(['msg' => 'Compo Price deleted successfully.']);
    }
}
