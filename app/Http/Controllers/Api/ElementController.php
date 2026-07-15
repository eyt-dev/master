<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Element;
use Illuminate\Http\Request;

class ElementController extends Controller
{
    /**
     * List all elements.
     */
    public function index(Request $request)
    {
        $elements = Element::orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data'    => $elements,
        ]);
    }

    /**
     * Show a single element.
     */
    public function show(Element $element)
    {
        return response()->json([
            'success' => true,
            'data'    => $element,
        ]);
    }
}
