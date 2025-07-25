<?php

declare(strict_types=1);

namespace Modules\Todo\Requests\Groups;

use BitCore\Application\Services\Requests\RequestValidator;

class ListAllGroupsRequest extends RequestValidator
{
    /**
     * Get the validation rules for lising todo group.
     */
    public static function rules(): array
    {
        return [
             'search' => 'nullable|string|max:50',
            'sort_by' => 'nullable|string|in:id,created_at',
            'order' => 'nullable|string|in:asc,desc',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100'
        ];
    }
}
