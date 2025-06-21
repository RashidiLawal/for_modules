<?php

declare(strict_types=1);

namespace BitCore\Modules\ModulesManager\Requests;

use BitCore\Application\Services\Requests\RequestValidator;

class CreateModuleRequest extends RequestValidator
{
    /**
     * Define validation rules for creating a module.
     *
     * @return array
     */
    public static function rules(): array
    {
        return [
            'file' => 'required|string',
            'metadata' => 'nullable|array',
        ];
    }
}
