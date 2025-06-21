<?php
declare(strict_types=1);

namespace Modules\Todo\Requests\Todos;
use BitCore\Application\Services\Requests\RequestValidator;


class UpdateTodoRequest extends RequestValidator
{
    /**
     * Validation rules
     */
    public static function rules(): array
    {
        return [
                'todo_title'             => 'required|string|max:255',
            'todo_description'       => 'required|string|max:255|alpha_dash',
            'todo_completed'        => 'nullable|boolean',
            'created_at'            => 'nullable|date',
            'updated_at'            => 'nullable|date',
            'deleted_at'            => 'nullable|date',
        ];
    }
    
    /**
     * Custom validation messages
     */
    public static function messages(): array
    {
        return [
            'title.string' => 'Title must be a string',
            'title.max' => 'Title cannot exceed 500 characters',
            'description.string' => 'Description must be a string',
            'completed.boolean' => 'Completed status must be true or false'
        ];
    }
}