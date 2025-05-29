<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCompoPriceRequest extends FormRequest
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
            Rule::requiredIf(fn() => !$this->boolean('set_last_date')),
        ];

        $rules['unit'] = [
            'nullable',
            Rule::requiredIf(fn() => !$this->boolean('set_last_unit')),
        ];

        if ($this->filled('pricing_date') && $componentId && $elementId) {
            $rules['pricing_date'][] = Rule::unique('compo_prices')->where(function ($query) use ($componentId, $elementId) {
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
                $lastDate = \App\Models\CompoPrice::orderByDesc('pricing_date')
                    ->orderByDesc('created_at')
                    ->value('pricing_date');

                if (!$lastDate) {
                    $validator->errors()->add('set_last_date', 'No previous pricing date found in the system. This is the first entry.');
                }
            }

            if ($this->boolean('set_last_unit')) {
                $lastUnit = \App\Models\CompoPrice::orderByDesc('created_at')
                    ->value('unit');

                if (!$lastUnit) {
                    $validator->errors()->add('set_last_unit', 'No previous unit found in the system. This is the first entry.');
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

    // Keep this method for backward compatibility if needed elsewhere
    protected function getComponentId()
    {
        return explode('_', $this->input('component'))[0] ?? null;
    }

}
