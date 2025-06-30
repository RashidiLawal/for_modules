<?php

declare(strict_types=1);

namespace Modules\Todo\Requests\Groups;

use BitCore\Application\Services\Requests\RequestValidator;

class CreateGroupRequest extends RequestValidator
{
    /**
     * Get the validation rules for creatind a todo group.
     */
    public static function rules(): array
    {
        return [
            'group_title'           => 'required|string|max:255',
            'group_description'     => 'required|string|max:255',
            'group_completed'        => 'nullable|boolean',
            'created_at'            => 'nullable|date',
            'updated_at'            => 'nullable|date',
            'deleted_at'            => 'nullable|date',
        ];
    }
}
