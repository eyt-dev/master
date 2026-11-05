<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\TypeMapping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class TypeMappingController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = TypeMapping::with('project')->orderBy('created_at', 'desc')->get();

            return datatables()->of($data)
                ->addColumn('url', function ($row) {
                    return "<a href='{$row->url}' target='_blank'>{$row->url}</a>";
                })
                ->addColumn('project', function ($row) {
                    return $row->project->project_name ?? 'N/A';
                })
                ->addColumn('created_at', function ($row) {
                    return $row->created_at->format('Y-m-d');
                })
                ->addColumn('action', function ($row) {
                    return '<a class="edit-type-mapping btn btn-sm btn-success mr-1" data-path="'.route('type-mapping.edit', ['username' => request()->segment(1), 'type_mapping' => $row->id]).'" title="Edit"><i class="fa fa-edit"></i></a>'
                         .'<a class="delete-type-mapping btn btn-sm btn-danger" data-id="'.$row->id.'" title="Delete"><i class="fa fa-trash"></i></a>';
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'url'])
                ->make(true);
        }

        return view('backend.type-mapping.index');
    }

    public function create()
    {
        $projects = Project::orderBy('project_name')->get();
        return view('backend.type-mapping.create', compact('projects'));
    }

    public function store(Request $request, $siteUrl)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'type' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'parent_type' => ['required', Rule::in(TypeMapping::PARENT_TYPES)],
        ]);

        TypeMapping::create([
            'project_id' => $request->project_id,
            'type' => $request->type,
            'url' => $request->url,
            'parent_type' => $request->parent_type,
            'created_by' => auth()->id(),
        ]);

        Session::flash('successMsg', 'Type mapping created successfully.');
        return redirect()->route('type-mapping.index', ['username' => request()->segment(1)]);
    }

    public function edit($siteUrl, $id)
    {
        $typeMapping = TypeMapping::findOrFail($id);
        $projects = Project::orderBy('project_name')->get();
        return view('backend.type-mapping.create', compact('typeMapping', 'projects'));
    }

    public function update(Request $request, $siteUrl, $id)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'type' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'parent_type' => ['required', Rule::in(TypeMapping::PARENT_TYPES)],
        ]);

        $typeMapping = TypeMapping::findOrFail($id);
        $typeMapping->update([
            'project_id' => $request->project_id,
            'type' => $request->type,
            'url' => $request->url,
            'parent_type' => $request->parent_type,
        ]);

        Session::flash('successMsg', 'Type mapping updated successfully.');
        return redirect()->route('type-mapping.index', ['username' => request()->segment(1)]);
    }

    public function destroy($siteUrl, $id)
    {
        TypeMapping::findOrFail($id)->delete();
        return response()->json(['msg' => 'Type mapping deleted successfully.']);
    }
}
