<?php
namespace Netto\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Netto\Models\{Currency, Delivery};

class DeliveryRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return array_merge(
            get_rules_multilingual([
                'name' => ['required', 'string', 'max:255'],
                'description' => ['nullable'],
            ]),
            [
                'sort' => ['integer', 'min:0', 'max:255'],
                'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9\-]+$/', Rule::unique(Delivery::class, 'slug')->ignore($this->get('id'))],
                'is_active' => ['in:1,0'],
                'cost' => ['decimal:0,2', 'min:0', 'max:999999.99'],
                'currency_id' => ['required', 'integer', 'exists:'.Currency::class.',id'],
                'total_min' => ['nullable', 'decimal:2', 'min:0', 'max:999999.99'],
                'total_max' => ['nullable', 'decimal:2', 'min:0', 'max:999999.99'],
                'volume_min' => ['nullable', 'decimal:1,8', 'min:0', 'max:99999999.99999999'],
                'volume_max' => ['nullable', 'decimal:1,8', 'min:0', 'max:99999999.99999999'],
                'weight_min' => ['nullable', 'integer', 'min:0', 'max:16777215'],
                'weight_max' => ['nullable', 'integer', 'min:0', 'max:16777215'],
            ]
        );
    }
}
