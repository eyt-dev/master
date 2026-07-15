<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Form;
use Illuminate\Http\Request;

class FormController extends Controller
{
    /**
     * List all forms.
     */
    public function index(Request $request)
    {
        $forms = Form::orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data'    => $forms,
        ]);
    }

    /**
     * Show a single form.
     */
    public function show(Form $form)
    {
        return response()->json([
            'success' => true,
            'data'    => $form,
        ]);
    }
}
