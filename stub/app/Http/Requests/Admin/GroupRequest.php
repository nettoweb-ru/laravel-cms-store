<?php
namespace App\Http\Requests\Admin;

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

        return [
            'name' => ['required', 'string', 'max:255'],
            'sort' => ['integer', 'min:0', 'max:16777215'],
            'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9\-]+$/', new UniqueGroupSlug($this->get('id'), $this->get('parent_id'))],
            'parent_id' => ['nullable', 'integer', "exists:{$class},id"],
            'is_active' => ['in:1,0'],
        ];

    }
}
