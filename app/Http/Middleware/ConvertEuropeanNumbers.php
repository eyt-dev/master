<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ConvertEuropeanNumbers
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->isJson()) {
            $data = $request->json()->all();
            $data = $this->convertNumbers($data);
            $request->json()->replace($data);
        }

        return $next($request);
    }

    private function convertNumbers($data)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->convertNumbers($value);
            } elseif (is_string($value) && $this->isEuropeanNumber($value)) {
                $data[$key] = str_replace(',', '.', $value);
            }
        }

        return $data;
    }

    private function isEuropeanNumber($value)
    {
        return preg_match('/^\d+,\d+$/', $value) || preg_match('/^\d+,\d+,\d+$/', $value);
    }
}
