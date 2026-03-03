<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        if ($componentId instanceof \App\Models\Component) {
            $componentId = $componentId->id;
        }

        return [
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('components', 'code')->ignore($componentId),
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'form' => ['required', 'exists:forms,id'],
            'unit' => ['required', 'exists:units,id'],
            'type' => ['required', 'in:1,2'],
            'elements' => ['sometimes', 'array'],
        ];
    }
}
