<?php

namespace Netto\Http\Requests\Admin;

use App\Models\{Merchandise, User};
use Illuminate\Foundation\Http\FormRequest;
use Netto\Models\{Currency, Delivery};

class OrderRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        $cartRules = [];
        foreach ($this->all() as $name => $value) {
            if (str_starts_with($name, 'cart|') || str_starts_with($name, 'cart_new|')) {
                $tmp = explode('|', $name);
                switch ($tmp[2]) {
                    case 'merchandise_id':
                        $cartRules[$name] = [
                            'nullable',
                            'exists:'.Merchandise::class.',id'
                        ];
                        break;
                    case 'price':
                        $cartRules[$name] = [
                            "required_with:{$tmp[0]}|{$tmp[1]}|merchandise_id",
                            'nullable',
                            'decimal:0,2',
                            'gt:0',
                            'max:999999.99'
                        ];
                        break;
                    case 'quantity':
                        $cartRules[$name] = [
                            "required_with:{$tmp[0]}|{$tmp[1]}|merchandise_id",
                            'nullable',
                            'integer',
                            'gt:0',
                            'max:16777215'
                        ];
                        break;
                }
            }
        }

        $statusId = implode(',', array_map(function($item) {return $item['id'];}, get_order_status_list()));
        return array_merge(
            $cartRules,
            [
                'user_id' => ['nullable', 'integer', 'exists:'.User::class.',id'],
                'status_id' => ['required', "in:{$statusId}"],
                'delivery_id' => ['required', 'integer', 'exists:'.Delivery::class.',id'],
                'delivery_cost' => ['decimal:0,2', 'min:0', 'max:999999.99'],
                'currency_id' => ['required', 'integer', 'exists:'.Currency::class.',id'],
            ]
        );
    }
}
