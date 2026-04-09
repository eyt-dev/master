<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class UpdateComponentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $componentId = $this->route('component');
        if ($componentId instanceof \App\Models\Component) {
            $componentId = $componentId->id;
        }

        return [
            'code' => [
                'required', 'string', 'max:255',
                Rule::unique('components', 'code')->ignore($componentId),
            ],
            'name' => ['required', 'string', 'max:255', Rule::unique('components', 'name')->ignore($componentId)],
            'description' => ['nullable', 'string', 'max:1000'],
            'form' => ['required', 'exists:forms,id'],
            'type' => ['required', 'in:1,2,3'],
            'elements' => ['sometimes', 'array', 'min:1'],
            'elements.*.element_id' => ['required', 'exists:elements,id'],
            'elements.*.amount' => ['required', 'regex:/^\d+(\.\d{1,2})?$/', 'numeric', 'max:99999999.99'],
            'elements.*.element_unit_id' => ['required', 'exists:units,id'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif,webp'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json(['success' => false, 'errors' => $validator->errors()], 422)
        );
    }
}
