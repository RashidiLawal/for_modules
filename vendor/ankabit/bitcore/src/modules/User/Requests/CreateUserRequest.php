<?php

declare(strict_types=1);

namespace BitCore\Modules\User\Requests;

use BitCore\Application\Services\Requests\RequestValidator;

class CreateUserRequest extends RequestValidator
{
    /**
     * Get the validation rules that apply to the request.
     */
    public static function rules(): array
    {
        return ['email' => 'required|email', 'password' => 'required|min:6'];
    }
}
