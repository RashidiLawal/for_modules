<?php

declare(strict_types=1);

namespace Modules\Todo\Requests\Todos;

use BitCore\Application\Services\Requests\RequestValidator;

class CreateTodoRequest extends RequestValidator
{
    /**
     * Get the validation rules that apply to the request.
     */
    public static function rules(): array
    {
        return [
                'todo_title' => 'required|string|max:255',
                'todo_description' => 'required|string|max:255',
                'completed' => 'boolean',
                'created_at'   => 'sometimes|date',
                'updated_at'   => 'sometimes|date',
                'deleted_at'    => 'sometimes|date',
            ];
    }
}
