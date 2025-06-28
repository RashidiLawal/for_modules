<?php

declare(strict_types=1);

namespace Modules\Todo\Tests\Actions\Todos;

use Modules\Todo\Tests\TestCase;
use Modules\Todo\Tests\Traits\CreateTestTodos;

class CreateTodoActionTest extends TestCase
{
    use CreateTestTodos;

    /**
     * Generate route for creating a todo.
     */
    private function getRoute(): string
    {
        return $this->generateRouteUrl('todos.store');
    }

    /**
     * Test successful creation of a todo.
     */
    public function testCreateActionSuccess(): void
    {
        $app = $this->getAppInstance();

        $requestData = [
            'todo_title'       => 'Test Todo ' . uniqid(),
            'todo_description' => 'Test description for todo',
            'completed'        => false,
        ];

        $request = $this->createRequestWithCsrf($app, $this->getRoute(), 'POST', $requestData);
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals(201, $payload['statusCode']);
        $this->assertArrayHasKey('status', $payload['data']);
        $this->assertTrue($payload['data']['status']);
        $this->assertEquals(trans("Todo::messages.todo_created"), $payload['data']['message']);
        $this->assertArrayHasKey('todo', $payload['data']);
        $this->assertNotEmpty($payload['data']['todo']);
    }

    /**
     * Test creation of a todo with missing required fields.
     */
    public function testCreateTodoValidationError(): void
    {
        $app = $this->getAppInstance();

        $requestData = [
            // missing todo_title and todo_description
        ];

        $request = $this->createRequestWithCsrf($app, $this->getRoute(), 'POST', $requestData);
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
     * Test creation of a todo with duplicate title.
     */
    public function testCreateTodoDuplicateTitleError(): void
    {
        $app = $this->getAppInstance();

        // Create first todo
        $existingTodo = $this->createTodo();

        $requestData = [
            'todo_title'       => $existingTodo->getTodoTitle(),
            'todo_description' => 'Another description',
            'completed'        => false,
        ];

        $request = $this->createRequestWithCsrf($app, $this->getRoute(), 'POST', $requestData);
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        $this->assertEquals(409, $response->getStatusCode());
        $this->assertArrayHasKey('status', $payload['data']);
        $this->assertFalse($payload['data']['status']);
    }
}
