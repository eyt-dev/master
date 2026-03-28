<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StoreComponentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:255', 'unique:components,code'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'form' => ['required', 'exists:forms,id'],
            'type' => ['required', 'in:1,2,3'],
            'elements' => ['required', 'array', 'min:1'],
            'elements.*.element_id' => ['required', 'exists:elements,id'],
            'elements.*.amount' => ['required', 'regex:/^\d+(\.\d{1,2})?$/', 'numeric', 'max:99999999.99'],
            'elements.*.element_unit_id' => ['required', 'exists:units,id'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json(['success' => false, 'errors' => $validator->errors()], 422)
        );
    }
}
