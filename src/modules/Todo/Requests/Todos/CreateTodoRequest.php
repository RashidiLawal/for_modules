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
                'todo_title' => 'required|title|string|max:190',
                'todo_description' => 'requiured|string|max:500',
                'completed' => 'required|boolean',
                'created_at'   => 'required|date',
                'updated_at'   => 'required|date',
                'deleted_at'    => 'required|date',
            ];
    }
}
