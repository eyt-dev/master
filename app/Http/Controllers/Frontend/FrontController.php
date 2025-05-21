<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class FrontController extends Controller
{
    public function index() {
        // $data = array();
        // return view('frontend.home', ['data' => $data]);

        $domain = request()->getHost();
        $setting = Setting::where('domain', $domain)
            ->orWhere('admin_domain', $domain)    
            ->with('themes')->first();//admin_domain
        $admin = $setting->creator;

        // Load appropriate theme (stored in DB or config)
        $theme = $setting->themes ? $setting->themes->name : '';

        return view("frontend.$theme.home", compact('admin'));
    }
}
