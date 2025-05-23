<?php

namespace Netto\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Netto\Models\OrderStatus;
use Netto\Rules\UniqueDefaultEntity;

class OrderStatusRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return array_merge(
            get_rules_multilingual([
                'name' => ['required', 'string', 'max:255'],
            ]),
            [
                'slug' => ['required', 'string', 'max:255', 'lowercase', 'alpha:ascii', Rule::unique(OrderStatus::class, 'slug')->ignore($this->get('id'))],
                'is_default' => ['in:1,0', new UniqueDefaultEntity(OrderStatus::class, $this->get('id'), $this->get('is_default'))],
            ]
        );
    }
}
