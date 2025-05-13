<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Admin;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        // if (auth()->user()->role !== 'SuperAdmin') {
            // Redirect to create view with their setting (or a blank form if none exists)
            $setting = Setting::where('created_by', auth()->id())->first();

            // If setting exists, send to edit
            if ($setting) {
                return redirect()->route('setting.edit', ['site' => $request->route('site'), 'setting' => $setting->id]);
            }

            // Else send to create
            return redirect()->route('setting.create');
        // }

        if ($request->ajax()) {
            $data = Setting::orderBy('created_at', 'desc')->get();

            return datatables()->of($data)
                ->addIndexColumn()
                ->addColumn('domain', function($row) {
                    return $row->domain ?? 'N/A';
                })
               ->addColumn('created_by', function($row) {
                    return $row->creator->name ?? 'N/A';
                })
                ->addColumn('action', function($row) {
                    $editUrl = route('setting.edit', $row->id);
                    $deleteUrl = route('setting.destroy', $row->id);

                    $btn = '<a href="'. $editUrl .'" class="edit-setting btn btn-sm btn-info"><i class="fa fa-edit"></i></a> ';
                    $btn .= '<a href="javascript:void(0);" data-id="'.$row->id.'" class="delete-setting btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.setting.index');
    }

    public function create($admin = '')
    {
        if (auth()->user()->role !== 'SuperAdmin') {
            $created_by = auth()->user()->id;
            $setting = Setting::where('created_by', $created_by)->first();
            if ($setting) {
                return redirect()->route('setting.edit', $setting->getKey());
            }
        }
        
        $setting = new Setting(); 
        $admins = Admin::get(); 
        if($admin) {
            $setting->created_by = $admin;
        }
        return view('backend.setting.create', compact('setting','admins'));
    }

    public function edit($site, $id)
    {
        // dd($id);
        $setting = Setting::find($id);
        $admins = Admin::get(); 
        return view('backend.setting.create', compact('setting','admins'));
    }

    public function store(Request $request)
    {
       
        if (auth()->user()->role !== 'SuperAdmin') {
            $request['created_by'] = auth()->user()->id;
        } 
        
        $data = Setting::where("created_by", $request->created_by)->count();

        if($data){
            return redirect()->back()->with('error', 'Settings already exists.');
        }        

        $request->validate([
            'domain' => 'required|nullable|url',
            'admin_domain' => 'required|nullable|url',
            'created_by' => 'required',

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
        ]);

        $data = $request->except('_token');

        foreach (['dark_logo', 'light_logo', 'footer_logo', 'favicon'] as $field) {
            if ($request->hasFile($field)) {
                $data[$field] = $request->file($field)->store('settings', 'public');
            }
        }
        $data["domain"] = parse_url($request->domain, PHP_URL_HOST);
        $data["admin_domain"] = parse_url($request->admin_domain, PHP_URL_HOST);
        Setting::create($data);
        Session::flash('successMsg', 'Settings saved successfully.');
        return redirect()->route('setting.index');
    }    

    public function update(Request $request, $id)
    {
        $setting = Setting::find($id);

        $request->validate([
            'domain' => 'required|nullable|url',
            'admin_domain' => 'required|nullable|url',

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
        ]);

        $data = $request->except('_token');

        foreach (['dark_logo', 'light_logo', 'footer_logo', 'favicon'] as $field) {
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
        $data["domain"] = parse_url($request->domain, PHP_URL_HOST);
        $data["admin_domain"] = parse_url($request->admin_domain, PHP_URL_HOST);
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

    public function checkSetting($created_by)
    {
        $setting = Setting::where('created_by', $created_by)->first();

        if ($setting) {
            return response()->json([
                'exists' => true,
                'setting_id' => $setting->getKey(), // Primary key (id)
            ]);
        } else {
            return response()->json([
                'exists' => false,
                'admin' => $created_by,
            ]);
        }
    }

}
