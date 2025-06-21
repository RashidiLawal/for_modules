<?php
declare(strict_types=1);

namespace Modules\Todo\Requests\Todos;

use BitCore\Application\Services\Requests\RequestValidator;

class DeleteTodoRequest extends RequestValidator
{
    /**
     * Validation rules (empty since we only need the ID from route)
     */
    public static function rules(): array
    {
        return [];
    }

    /**
     * Custom validation messages
     */
    public static function messages(): array
    {
        return [];
    }
}