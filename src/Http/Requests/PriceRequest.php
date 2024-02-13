<?php
namespace Netto\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Netto\Models\Price;
use Netto\Rules\UniqueDefaultEntity;

class PriceRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'lowercase', 'alpha:ascii', Rule::unique(Price::class, 'slug')->ignore($this->get('id'))],
            'is_default' => ['in:1,0', new UniqueDefaultEntity(Price::class, $this->get('id'), $this->get('is_default'))],
        ];
    }
}
