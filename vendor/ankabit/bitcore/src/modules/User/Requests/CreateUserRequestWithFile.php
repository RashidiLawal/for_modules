<?php

declare(strict_types=1);

namespace BitCore\Modules\User\Requests;

use BitCore\Application\Services\Requests\RequestValidator;

class CreateUserRequestWithFile extends RequestValidator
{
    /**
     * Get the validation rules that apply to the request.
     */
    public static function rules(): array
    {
        return [
            'file.*' => 'required|mimetypes:application/json|max:10240'
        ];
    }
}
