<?php
namespace Netto\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Netto\Models\Currency;
use Netto\Models\Delivery;

class DeliveryRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        $currency = Currency::class;
        $delivery = Delivery::class;
        $object = new $delivery();

        return array_merge(
            $object->getMultiLangRules([
                'title' => ['required', 'string', 'max:255'],
                'description' => ['nullable'],
            ]),
            [
                'sort' => ['integer', 'min:0', 'max:16777215'],
                'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9\-]+$/', Rule::unique($delivery, 'slug')->ignore($this->get('id'))],
                'is_active' => ['in:1,0'],
                'cost' => ['decimal:0,2', 'min:0', 'max:999999.99'],
                'currency_id' => ['required', 'integer', "exists:{$currency},id"],
                'total_min' => ['decimal:2', 'min:0', 'max:999999.99'],
                'total_max' => ['decimal:2', 'min:0', 'max:999999.99'],
                'volume_min' => ['decimal:1,8', 'min:0', 'max:99999999.99999999'],
                'volume_max' => ['decimal:1,8', 'min:0', 'max:99999999.99999999'],
                'weight_min' => ['integer', 'min:0', 'max:16777215'],
                'weight_max' => ['integer', 'min:0', 'max:16777215'],
            ]
        );
    }
}
