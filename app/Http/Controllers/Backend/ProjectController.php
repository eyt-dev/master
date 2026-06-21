<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Admin;
use Illuminate\Support\Facades\Session;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Project::with('admins', 'userTypes')
                ->when(auth()->user()->role !== 'SuperAdmin', function ($query) {
                    $query->whereHas('admins', function ($query) {
                        $query->where('admin_id', auth()->id());
                    });
                })
                ->orderBy('created_at', 'desc')
                ->get();

            return datatables()->of($data)
                ->addColumn('admins', function ($row) {
                    return $row->admins->pluck('name')->join(', ') ?: 'N/A';
                })
                ->addColumn('user_types', function ($row) {
                    return $row->userTypes->pluck('user_type')->join(', ') ?: 'N/A';
                })
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
        $admins = Admin::where('type', 1)->get();
        return view('backend.project.create', compact('admins'));
    }

    public function store(Request $request, $siteUrl)
    {
        $request->validate([
            'project_name' => 'required|string|max:255',
            'description' => 'nullable',
            'url' => 'nullable|url',
            'admins' => 'required|array',
            'admins.*' => 'exists:admins,id',
            'user_types' => 'required|array|min:1',
            'user_types.*' => 'required|string|max:255',
        ]);

        $userTypes = array_filter(array_unique($request->user_types));
        if (count($userTypes) !== count($request->user_types)) {
            return back()->withErrors(['user_types' => 'Duplicate user types are not allowed.'])->withInput();
        }

        $project = Project::create([
            'project_name' => $request->project_name,
            'description' => $request->description,
            'url' => $request->url,
        ]);

        $project->admins()->sync($request->admins);

        foreach ($userTypes as $userType) {
            $project->userTypes()->create(['user_type' => $userType]);
        }

        Session::flash('successMsg', 'Project created successfully.');
        return redirect()->route('project.index', ['username' => request()->segment(1)]);
    }

    public function edit($siteUrl, $id)
    {
        $project = Project::findOrFail($id);
        $admins = Admin::where('type', 1)->get();
        return view('backend.project.create', compact('project', 'admins'));
    }

    public function update(Request $request, $siteUrl, $id)
    {
        $request->validate([
            'project_name' => 'required|string|max:255',
            'description' => 'nullable',
            'url' => 'nullable|url',
            'admins' => 'required|array',
            'admins.*' => 'exists:admins,id',
            'user_types' => 'required|array|min:1',
            'user_types.*' => 'required|string|max:255',
        ]);

        $userTypes = array_filter(array_unique($request->user_types));
        if (count($userTypes) !== count($request->user_types)) {
            return back()->withErrors(['user_types' => 'Duplicate user types are not allowed.'])->withInput();
        }

        $project = Project::findOrFail($id);
        $project->update([
            'project_name' => $request->project_name,
            'description' => $request->description,
            'url' => $request->url,
        ]);

        $project->admins()->sync($request->admins);

        $project->userTypes()->delete();
        foreach ($userTypes as $userType) {
            $project->userTypes()->create(['user_type' => $userType]);
        }

        Session::flash('successMsg', 'Project updated successfully.');
        return redirect()->route('project.index', ['username' => request()->segment(1)]);
    }

    public function destroy($siteUrl, $id)
    {
        Project::findOrFail($id)->delete();
        return response()->json(['msg' => 'Project deleted successfully.']);
    }
}
