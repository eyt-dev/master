<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreComponentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'form' => ['required', 'exists:forms,id'],
            'unit' => ['required', 'exists:units,id'],
            'type' => ['required', 'in:1,2'],

            // elements array validation
            'elements' => ['required', 'array', 'min:1'],
            //  'elements.*.element_id' => ['required', 'exists:elements,id'],
            // 'elements.*.amount' => ['required', 'numeric'],
        ];
    }
}
