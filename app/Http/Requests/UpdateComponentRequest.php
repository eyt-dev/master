<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateComponentRequest extends FormRequest
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
        $componentId = $this->route('component');

        return [
            'code' => ['required', 'string', 'max:255','unique:component,code'.$componentId],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'form' => ['required', 'exists:forms,id'],
            'unit' => ['required', 'exists:units,id'],
            'type' => ['required', 'in:1,2'],
            // Make elements optional in the request rules, as we'll handle it in the controller
            'elements' => ['sometimes', 'array'],
          //  'elements.*.element_id' => ['required_with:elements', 'exists:elements,id'],
          //  'elements.*.amount' => ['required_with:elements.*.element_id', 'numeric'],
        ];
    }
}
