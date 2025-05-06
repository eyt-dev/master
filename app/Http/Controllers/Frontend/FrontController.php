<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function index() {
        // $data = array();
        // return view('frontend.home', ['data' => $data]);

        $domain = $request->getHost();
        $setting = Setting::where('domain', $domain)->first();//admin_domain
        $admin = $setting->creator;

        // Load appropriate theme (stored in DB or config)
        $theme = $admin->theme ?? '';

        return view("themes.$theme.homepage", compact('admin'));
    }
}
