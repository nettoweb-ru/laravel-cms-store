<?php

namespace Netto\Http\Requests\Admin;

use App\Models\Section;
use Illuminate\Foundation\Http\FormRequest;
use Netto\Models\Album;
use Netto\Rules\UniqueSectionSlug;

class SectionRequest extends FormRequest
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
            [
                'sort' => ['integer', 'min:0', 'max:16777215'],
                'album_id' => ['nullable', 'integer', 'exists:'.Album::class.',id'],
                'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9\-]+$/', new UniqueSectionSlug($this->get('id'), $this->get('parent_id'))],
                'parent_id' => ['nullable', 'integer', 'exists:'.Section::class.',id'],
                'is_active' => ['in:1,0'],
            ]
        );
    }
}
