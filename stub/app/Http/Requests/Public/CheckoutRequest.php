<?php

namespace App\Http\Requests\Public;

use Netto\Http\Requests\Public\CheckoutRequest as BaseRequest;

class CheckoutRequest extends BaseRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return parent::rules();
    }
}
