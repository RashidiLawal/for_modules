<?php

declare(strict_types=1);

namespace BitCore\Modules\ModulesManager\Requests;

use BitCore\Application\Services\Requests\RequestValidator;

/**
 * Class UpdateModuleStatusRequest
 *
 * Validates input for updating a module's activation status.
 */
class UpdateModuleStatusRequest extends RequestValidator
{
    /**
     * Get the validation rules for updating module status.
     */
    public static function rules(): array
    {
        return [
            'name'        => 'nullable|string|max:100',
            'type'   => 'nullable|string|max:150',
            'priority' => 'nullable|string|max:255',
            'entry' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'plan' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
            'images'     => 'nullable|string|max:20',
        ];
    }
}
