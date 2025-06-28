<?php

declare(strict_types=1);

namespace Modules\Todo\Tests\Traits;

use BitCore\Foundation\Carbon;
use Modules\Todo\Models\Todo;

trait CreateTestTodos
{
    /**
     * 
     * Creates a test todo record.
     *
     * @param array $data Custom todo data to override defaults.
     * @return Todo
     */
    public function createTodo(array $data = []): Todo
    {
        return Todo::create(array_merge([
            'todo_title'          => 'Test Todo ' . uniqid(),
            'todo_description'    => 'Description for Todo-' . uniqid(),
            'todo_slug'          => 'test-todo-' . uniqid(),
            'completed'          => true,
            'group_id'          => 1, // Default group ID, adjust as needed
        ], $data));
    }
    
    public function getTodoTitle(): string
{
    return $this->todo_title;
}

}
