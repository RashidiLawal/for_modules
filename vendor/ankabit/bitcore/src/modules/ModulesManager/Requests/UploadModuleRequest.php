<?php

declare(strict_types=1);

namespace BitCore\Modules\ModulesManager\Requests;

use BitCore\Application\Services\Requests\RequestValidator;

class UploadModuleRequest extends RequestValidator
{
    /**
     * Define validation rules for uploading a module file.
     *
     * @return array
     */
    public static function rules(): array
    {
        return [
            'file' => 'required|file|mimes:zip,json',
        ];
    }
}
