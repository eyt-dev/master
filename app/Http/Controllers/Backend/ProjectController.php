<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Project::orderBy('created_at', 'desc')->get();

            return datatables()->of($data)
                ->addColumn('url', function ($row) {
                    return "<a href='{$row->url}' target='_blank'>{$row->url}</a>" ?: 'N/A';
                })
                ->addColumn('created_at', function ($row) {
                    return $row->created_at->format('Y-m-d');
                })
                ->addColumn('action', function ($row) {
                    return '<a class="edit-project btn btn-sm btn-success mr-1" data-path="'.route('project.edit', ['username' => request()->segment(1), 'project' => $row->id]).'" title="Edit"><i class="fa fa-edit"></i></a>'
                         .'<a class="delete-project btn btn-sm btn-danger" data-id="'.$row->id.'" title="Delete"><i class="fa fa-trash"></i></a>';
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'url'])
                ->make(true);
        }

        return view('backend.project.index');
    }

    public function create()
    {
        return view('backend.project.create');
    }

    public function store(Request $request, $siteUrl)
    {
        $request->validate([
            'project_name' => 'required|string|max:255',
            'description' => 'nullable',
            'url' => 'nullable|url',
        ]);

        $project = Project::create([
            'project_name' => $request->project_name,
            'description' => $request->description,
            'url' => $request->url,
            'created_by' => auth()->id(),
        ]);

        Session::flash('successMsg', 'Project created successfully.');
        return redirect()->route('project.index', ['username' => request()->segment(1)]);
    }

    public function edit($siteUrl, $id)
    {
        $project = Project::findOrFail($id);
        return view('backend.project.create', compact('project'));
    }

    public function update(Request $request, $siteUrl, $id)
    {
        $request->validate([
            'project_name' => 'required|string|max:255',
            'description' => 'nullable',
            'url' => 'nullable|url',
        ]);

        $project = Project::findOrFail($id);
        $project->update([
            'project_name' => $request->project_name,
            'description' => $request->description,
            'url' => $request->url,
        ]);

        Session::flash('successMsg', 'Project updated successfully.');
        return redirect()->route('project.index', ['username' => request()->segment(1)]);
    }

    public function destroy($siteUrl, $id)
    {
        Project::findOrFail($id)->delete();
        return response()->json(['msg' => 'Project deleted successfully.']);
    }
}
