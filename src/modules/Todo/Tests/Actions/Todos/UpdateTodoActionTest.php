<?php

declare(strict_types=1);

namespace Modules\Todo\Tests\Actions\Todos;

use Modules\Todo\Tests\TestCase;
use Modules\Todo\Tests\Traits\CreateTestTodos;

class UpdateTodoActionTest extends TestCase
{
    use CreateTestTodos;

    /**
     * Generate route for updating a todo by ID.
     */
    private function getRoute(int $id): string
    {
        return $this->generateRouteUrl('todos.update', ['id' => $id]);
    }

    /**
     * Test successful update of a todo.
     */
    public function testUpdateTodoActionSuccess(): void
    {
        $app = $this->getAppInstance();

        // Create a todo to update
        $todo = $this->createTodo();

        $requestData = [
            'todo_title'       => 'Updated Todo ' . uniqid(),
            'todo_description' => 'Updated description',
            'completed'        => true,
        ];

        $request = $this->createRequestWithCsrf($app, $this->getRoute($todo->id), 'PUT', $requestData);
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(200, $payload['statusCode']);
        $this->assertArrayHasKey('status', $payload['data']);
        $this->assertTrue($payload['data']['status']);
        $this->assertEquals(trans("Todo::messages.todo_updated"), $payload['data']['message']);
        $this->assertArrayHasKey('todo', $payload['data']);
        $this->assertEquals($requestData['todo_title'], $payload['data']['todo']['todo_title']);
    }

    /**
     * Test update of a todo with missing required fields.
     */
    public function testUpdateTodoValidationError(): void
    {
        $app = $this->getAppInstance();

        // Create a todo to update
        $todo = $this->createTodo();

        $requestData = [
            // missing todo_title and todo_description
        ];

        $request = $this->createRequestWithCsrf($app, $this->getRoute($todo->id), 'PUT', $requestData);
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals(422, $payload['statusCode']);
        $this->assertArrayHasKey('status', $payload['data']);
        $this->assertFalse($payload['data']['status']);
        $this->assertArrayHasKey('errors', $payload['data']);
        $this->assertNotEmpty($payload['data']['errors']);
    }

    /**
     * Test update of a non-existent todo.
     */
    public function testUpdateTodoNotFound(): void
    {
        $app = $this->getAppInstance();

        $requestData = [
            'todo_title'       => 'Updated Todo',
            'todo_description' => 'Updated description',
            'completed'        => true,
        ];

        $request = $this->createRequestWithCsrf($app, $this->getRoute(99999), 'PUT', $requestData);
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertArrayHasKey('status', $payload['data']);
        $this->assertFalse($payload['data']['status']);
    }

    /**
     * Test update of a todo with duplicate title.
     */
    public function testUpdateTodoDuplicateTitleError(): void
    {
        $app = $this->getAppInstance();

        // Create two todos
        $todo1 = $this->createTodo(['todo_title' => 'First Todo']);
        $todo2 = $this->createTodo(['todo_title' => 'Second Todo']);

        // Try to update todo2 with todo1's title
        $requestData = [
            'todo_title'       => $todo1->todo_title,
            'todo_description' => 'Updated description',
            'completed'        => true,
        ];

        $request = $this->createRequestWithCsrf($app, $this->getRoute($todo2->id), 'PUT', $requestData);
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        $this->assertEquals(409, $response->getStatusCode());
        $this->assertArrayHasKey('status', $payload['data']);
        $this->assertFalse($payload['data']['status']);
    }
}
