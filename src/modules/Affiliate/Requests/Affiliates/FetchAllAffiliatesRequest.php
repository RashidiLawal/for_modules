<?php

declare(strict_types=1);

namespace Modules\Affiliate\Requests\Affiliates;

use BitCore\Application\Services\Requests\RequestValidator;

class FetchAllAffiliatesRequest extends RequestValidator
{
    /**
     * Get the validation rules for creating a Payment Mode.
     */
    public static function rules(): array
    {
        return [
            'search' => 'nullable|string|max:50',
            'sort_by' => 'nullable|string|symbol,created_at',
            'order' => 'nullable|string|in:asc,desc',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100'
        ];
    }
}
