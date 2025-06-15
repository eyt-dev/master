<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Admin;

class FrontController extends Controller
{
    public function index($username=null) {
        // $data = array();
        // return view('frontend.home', ['data' => $data]);

        $domain = request()->getHost();
        $username = request()->route('username');
        
        if (! $username) {
            $setting = Setting::where('domain', $domain)  
                ->with('themes')->first();//admin_domain
            $admin = $setting->creator;
        } else {
            $admin = Admin::where('username', $username)->first();
            $setting = $admin->setting;
        }

        // Load appropriate theme (stored in DB or config)
        $theme = $setting->themes ? $setting->themes->name : '';

        return view("frontend.$theme.home", compact('admin'));
    }
}
