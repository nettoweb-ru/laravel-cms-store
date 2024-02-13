<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Netto\Models\OrderStatus;

class OrderRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        $class = OrderStatus::class;
        return [
            'status_id' => ['required', 'integer', "exists:{$class},id"],
        ];
    }
}
