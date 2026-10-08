<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MaterialName;
use App\Models\MaterialType;
use App\Models\BreedCategoryMaterialType;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class FeedMaterialController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = MaterialName::with('creator', 'materialType')
                ->when(auth()->user()->role !== 'SuperAdmin', function ($query) {
                    $query->where('created_by', auth()->id());
                })
                ->orderBy('created_at', 'desc')->get();
            return datatables()->of($data)
                ->addColumn('type', function($row) {
                    return $row->materialType?->name ?? 'N/A';
                })
                ->addColumn('creator', function($row) {
                    return $row->creator->name ?? 'N/A';
                })
                ->addColumn('action', function($row) {
                    return '<a class="edit-feed-material btn btn-sm btn-success" data-path="'.route('feedmaterial.edit', ['username' => request()->segment(1),  'feedmaterial' => $row->id]).'" title="Edit"><i class="fa fa-edit"></i></a>'
                         .'<a class="delete-feed-material btn btn-sm btn-danger" data-id="'.$row->id.'" title="Delete"><i class="fa fa-trash"></i></a>';
                })
                ->addIndexColumn()
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.feedmaterial.index');
    }

    public function create()
    {
        $materialTypes = MaterialType::orderBy('id')->get();
        return view('backend.feedmaterial.create', compact('materialTypes'));
    }

    public function store(Request $request, $siteUrl)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:material_names',
            'material_type_id' => 'required|integer|exists:material_types,id',
        ]);

        $createData = [
            'name' => $request->name,
            'material_type_id' => $request->material_type_id,
            'created_by' => auth()->id()
        ];
        MaterialName::create($createData);

        Session::flash('successMsg', 'Feed Material created successfully.');
        return redirect()->route('feedmaterial.index', ['username' => request()->segment(1)]);
    }

    public function edit($siteUrl, $id)
    {
        $feedmaterial = MaterialName::findOrFail($id);
        $materialTypes = MaterialType::orderBy('id')->get();
        return view('backend.feedmaterial.create', compact('feedmaterial', 'materialTypes'));
    }

    public function update(Request $request, $siteUrl, $id)
    {
        $feedmaterial = MaterialName::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255|unique:material_names,name,' . $id,
            'material_type_id' => 'required|integer|exists:material_types,id',
        ]);
        $feedmaterial->update([
            'name' => $request->name,
            'material_type_id' => $request->material_type_id,
        ]);

        Session::flash('successMsg', 'Feed Material updated successfully.');
        return redirect()->route('feedmaterial.index', ['username' => request()->segment(1)]);
    }

    public function destroy($siteUrl, $id)
    {
        MaterialName::findOrFail($id)->delete();
        return response()->json(['msg' => 'Feed Material deleted successfully.']);
    }

    public function loadBreedCategoryMapping(Request $request)
    {
        $breedCategories = ['Broiler', 'Layer'];
        $materialTypes = MaterialType::orderBy('name')->get();

        $data = [];
        foreach ($breedCategories as $category) {
            $mappedTypeIds = BreedCategoryMaterialType::where('breed_category', $category)
                ->pluck('material_type_id')
                ->toArray();

            $data[] = [
                'breed_category' => $category,
                'material_types' => $materialTypes->map(function ($type) use ($mappedTypeIds) {
                    return [
                        'id' => $type->id,
                        'name' => $type->name,
                        'checked' => in_array($type->id, $mappedTypeIds),
                    ];
                }),
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function saveBreedCategoryMapping(Request $request)
    {
        $request->validate([
            'mappings' => 'required|array',
            'mappings.*.breed_category' => 'required|in:Broiler,Layer',
            'mappings.*.material_type_ids' => 'required|array',
            'mappings.*.material_type_ids.*' => 'integer|exists:material_types,id',
        ]);

        try {
            foreach ($request->mappings as $mapping) {
                $category = $mapping['breed_category'];
                $typeIds = $mapping['material_type_ids'] ?? [];

                BreedCategoryMaterialType::where('breed_category', $category)->delete();

                foreach ($typeIds as $typeId) {
                    BreedCategoryMaterialType::create([
                        'breed_category' => $category,
                        'material_type_id' => $typeId,
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Breed category material mappings saved successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save mappings: ' . $e->getMessage(),
            ], 500);
        }
    }
}
