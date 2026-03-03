<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompoPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        [$componentId, $elementId] = $this->getComponentAndElementIds();

        $rules = [
            'component' => 'required',
            'price' => 'required|numeric',
            'set_last_date' => 'nullable|boolean',
            'set_last_unit' => 'nullable|boolean',
        ];

        $rules['pricing_date'] = [
            'nullable',
            Rule::requiredIf(fn () => !$this->boolean('set_last_date')),
        ];

        $rules['unit'] = [
            'nullable',
            Rule::requiredIf(fn () => !$this->boolean('set_last_unit')),
        ];

        // Check uniqueness only if pricing_date is provided manually
        if ($this->filled('pricing_date') && $componentId && $elementId) {
            $rules['pricing_date'][] = Rule::unique('compo_prices')
                ->ignore($this->route('compo_price')->id)
                ->where(function ($query) use ($componentId, $elementId) {
                    return $query->where('component_id', $componentId)
                        ->where('element_id', $elementId);
                });
        }

        return $rules;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'set_last_date' => $this->boolean('set_last_date'),
            'set_last_unit' => $this->boolean('set_last_unit'),
        ]);
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->boolean('set_last_date')) {
                // Get last date excluding current record
                $lastDate = \App\Models\CompoPrice::where('id', '!=', $this->route('compo_price')->id)
                    ->orderByDesc('pricing_date')
                    ->orderByDesc('created_at')
                    ->value('pricing_date');

                if (!$lastDate) {
                    $validator->errors()->add('set_last_date', 'No other pricing date found in the system to use as default.');
                }
            }

            if ($this->boolean('set_last_unit')) {
                // Get last unit excluding current record
                $lastUnit = \App\Models\CompoPrice::where('id', '!=', $this->route('compo_price')->id)
                    ->orderByDesc('created_at')
                    ->value('unit');

                if (!$lastUnit) {
                    $validator->errors()->add('set_last_unit', 'No other unit found in the system to use as default.');
                }
            }
        });
    }

    protected function getComponentAndElementIds()
    {
        $parts = explode('_', $this->input('component'));
        return [
            $parts[0] ?? null,
            $parts[1] ?? null
        ];
    }
}
