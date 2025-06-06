<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    // Store a new module
    public function store(Request $request, $siteUrl)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255|unique:modules,name',
        ]);
    
        // Save the module
        $module = Module::create([
            'name' => $request->name,
        ]);
        
        // Return JSON response
        return response()->json(['module' => $module,
        ]);
    }

    public function create($siteUrl)
    {
        return view('backend.permission.module');
    }
}

