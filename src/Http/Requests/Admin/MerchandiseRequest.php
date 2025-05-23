<?php

namespace Netto\Http\Requests\Admin;

use App\Models\Merchandise;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Netto\Models\Album;

class MerchandiseRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return array_merge(
            get_rules_upload([
                'photo' => ['nullable', 'mimes:jpg,png,gif,webp'],
            ]),
            get_rules_multilingual([
                'name' => ['required', 'string', 'max:255'],
                'description' => ['nullable'],
                'meta_title' => ['nullable', 'max:255'],
                'meta_keywords' => ['nullable'],
                'meta_description' => ['nullable'],
                'og_title' => ['nullable', 'max:255'],
                'og_description' => ['nullable'],
            ]),
            get_rules_costs(),
            [
                'sort' => ['integer', 'min:0', 'max:16777215'],
                'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9\-]+$/', Rule::unique(Merchandise::class, 'slug')->ignore($this->get('id'))],
                'is_active' => ['in:1,0'],
                'width' => ['integer', 'min:0', 'max:16777215'],
                'length' => ['integer', 'min:0', 'max:16777215'],
                'height' => ['integer', 'min:0', 'max:16777215'],
                'weight' => ['integer', 'min:0', 'max:16777215'],
                'album_id' => ['nullable', 'integer', 'exists:'.Album::class.',id'],
            ],
            [],
        );
    }
}
