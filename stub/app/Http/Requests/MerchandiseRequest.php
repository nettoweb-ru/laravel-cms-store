<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Netto\Models\Currency;
use App\Models\Merchandise;
use Netto\Services\PriceService;

class MerchandiseRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        $costRules = [];
        $currencyClass = Currency::class;

        foreach (PriceService::getList() as $price) {
            $costRules["costs|{$price['id']}|value"] = ['decimal:0,2', 'min:0', 'max:999999.99'];
            $costRules["costs|{$price['id']}|currency_id"] = ['required', 'integer', "exists:{$currencyClass},id"];
        }

        $object = new Merchandise();

        return array_merge(
            $object->getMultiLangRules([
                'title' => ['required', 'string', 'max:255'],
                'content' => ['nullable'],
                'meta_title' => ['nullable', 'max:255'],
                'meta_keywords' => ['nullable'],
                'meta_description' => ['nullable'],
                'og_title' => ['nullable', 'max:255'],
                'og_description' => ['nullable'],
            ]),
            [
                'sort' => ['integer', 'min:0', 'max:16777215'],
                'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9\-]+$/', Rule::unique(Merchandise::class, 'slug')->ignore($this->get('id'))],
                'is_active' => ['in:1,0'],
                'width' => ['integer', 'min:0', 'max:16777215'],
                'length' => ['integer', 'min:0', 'max:16777215'],
                'height' => ['integer', 'min:0', 'max:16777215'],
                'weight' => ['integer', 'min:0', 'max:16777215'],
            ],
            $costRules
        );
    }
}
