<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Group;
use Netto\Rules\UniqueGroupSlug;

class GroupRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        $class = Group::class;
        $object = new $class();

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
                'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9\-]+$/', new UniqueGroupSlug($this->get('id'), $this->get('parent_id'))],
                'parent_id' => ['nullable', 'integer', "exists:{$class},id"],
                'is_active' => ['in:1,0'],
            ],
        );
    }
}
