<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\StoreView;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Setting::when(auth()->user()->role !== 'SuperAdmin', function ($query) {
                $query->where('created_by', auth()->id());
            })
            ->orderBy('created_at', 'desc')->get();

            return datatables()->of($data)
                ->addIndexColumn()
                ->addColumn('store_view', function($row) {
                    return ($row->storeView->region . ' - ' . $row->storeView->language) ?? 'N/A';
                })
                ->addColumn('domain', function($row) {
                    return $row->domain ?? 'N/A';
                })
                ->addColumn('logo', function($row) {
                    if ($row->logo && Storage::disk('public')->exists($row->logo)) {
                        return '<img src="'.asset('storage/'.$row->logo).'" alt="Logo" height="50">';
                    }
                    return 'N/A';
                })
                ->addColumn('title', function($row) {
                    return $row->title ?? 'N/A';
                })
                ->addColumn('sub_title', function($row) {
                    return $row->sub_title ?? 'N/A';
                })
                ->addColumn('description', function($row) {
                    return Str::limit($row->description, 50) ?? 'N/A'; // limit long description
                })
                ->addColumn('created_by', function($row) {
                    return $row->creator->name ?? 'N/A';
                })
                ->addColumn('action', function($row) {
                    $editUrl = route('setting.edit', $row->id);
                    $deleteUrl = route('setting.destroy', $row->id);

                    $btn = '<a href="javascript:void(0);" data-id="'.$row->id.'" data-path="'.$editUrl.'" class="edit-setting btn btn-sm btn-info"><i class="fa fa-edit"></i></a> ';
                    $btn .= '<a href="javascript:void(0);" data-id="'.$row->id.'" class="delete-setting btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>';

                    return $btn;
                })
                ->rawColumns(['logo', 'action'])
                ->make(true);
        }
        return view('setting.index');
    }

    public function create()
    {
        $setting = Setting::first();
        $store_views = StoreView::get();
        return view('setting.create', compact('setting', 'store_views'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'store_view_id' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'sub_title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'dark_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'light_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'footer_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,ico|max:2048',
            'primary_text_color' => 'nullable|string|max:20',
            'secondary_text_color' => 'nullable|string|max:20',
            'primary_button_background' => 'nullable|string|max:20',
            'secondary_button_background' => 'nullable|string|max:20',
            'primary_button_text_color' => 'nullable|string|max:20',
            'secondary_button_text_color' => 'nullable|string|max:20',
            'domain' => 'nullable|url',
        ]);

        $data = $request->except('_token');
        $data['created_by'] = auth()->id();

        // dd($request->all());

        foreach (['logo', 'dark_logo', 'light_logo', 'footer_logo', 'favicon'] as $field) {
            if ($request->hasFile($field)) {
                $data[$field] = $request->file($field)->store('settings', 'public');
            }
        }

        Setting::create($data);

        Session::flash('successMsg', 'Settings saved successfully.');
        return redirect()->route('setting.index');
    }

    public function edit()
    {
        $setting = Setting::first();
        $store_views = StoreView::get();
        return view('setting.create', compact('setting','store_views'));
    }

    public function update(Request $request)
    {
        $setting = Setting::firstOrFail();

        $request->validate([
            'store_view_id' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'sub_title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'dark_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'light_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'footer_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,ico|max:2048',
            'primary_text_color' => 'nullable|string|max:20',
            'secondary_text_color' => 'nullable|string|max:20',
            'primary_button_background' => 'nullable|string|max:20',
            'secondary_button_background' => 'nullable|string|max:20',
            'primary_button_text_color' => 'nullable|string|max:20',
            'secondary_button_text_color' => 'nullable|string|max:20',
            'domain' => 'nullable|url',
        ]);

        $data = $request->except('_token');

        foreach (['logo', 'dark_logo', 'light_logo', 'footer_logo', 'favicon'] as $field) {
            if ($request->hasFile($field)) {
                // Delete the old file if exists
                if (!empty($setting->$field) && Storage::disk('public')->exists($setting->$field)) {
                    Storage::disk('public')->delete($setting->$field);
                }

                // Upload new file
                $data[$field] = $request->file($field)->store('settings', 'public');
            } else {
                // If no new file uploaded, keep the existing file
                $data[$field] = $setting->$field;
            }
        }

        $setting->update($data);

        Session::flash('successMsg', 'Settings updated successfully.');
        return redirect()->route('setting.index');
    }

    public function destroy()
    {
        $setting = Setting::first();
        if ($setting) {
            foreach (['logo', 'dark_logo', 'light_logo', 'footer_logo', 'favicon'] as $field) {
                if ($setting->$field && Storage::disk('public')->exists($setting->$field)) {
                    Storage::disk('public')->delete($setting->$field);
                }
            }
            $setting->delete();
        }
        return response()->json(['msg' => 'Settings deleted successfully.']);
    }
}
