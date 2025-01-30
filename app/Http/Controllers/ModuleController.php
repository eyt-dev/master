<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    // Store a new module
    public function store(Request $request)
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

    public function create()
    {
        return view('permission.module');
    }
}

