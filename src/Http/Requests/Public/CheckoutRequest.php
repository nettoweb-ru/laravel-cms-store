<?php

namespace Netto\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;
use Netto\Models\Delivery;

class CheckoutRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'delivery_id' => ['required', 'integer', 'exists:'.Delivery::class.',id'],
        ];
    }
}
