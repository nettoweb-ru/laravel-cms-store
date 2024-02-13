<?php

namespace Netto\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Group;

class UniqueGroupSlug implements ValidationRule
{
    private ?int $id;
    private ?int $parentId;

    /**
     * @param string|null $id
     * @param string|null $parentId
     */
    public function __construct(?string $id, ?string $parentId)
    {
        $this->parentId = (int) $parentId;
        if ($this->parentId === 0) {
            $this->parentId = null;
        }

        $this->id = (int) $id;
        if ($this->id === 0) {
            $this->id = null;
        }
    }

    /**
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (count(Group::select('id')->where('parent_id', $this->parentId)->where($attribute, $value)->whereNot('id', $this->id)->get())) {
            $fail(__('validation.unique'));
        }
    }
}
