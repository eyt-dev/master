<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompoPriceRequest extends FormRequest
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
            'component' => ['required'],
            'pricing_date' => [
                'required',
                Rule::unique('compo_prices')
                    ->ignore($this->route('compo_price')->id)
                    ->where(function ($query) {
                        return $query->where('component_id', $this->input('component'));
                    }),
            ],
            'price' => 'required|numeric',
            'description' => 'nullable|string',
        ];
    }
}
