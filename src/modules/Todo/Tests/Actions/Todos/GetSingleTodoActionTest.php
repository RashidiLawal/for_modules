<?php

declare(strict_types=1);

namespace Modules\Todo\Tests\Actions;

use Modules\Todo\Models\Todo;
use Modules\Todo\Tests\TestCase;
use Modules\Todo\Tests\Traits\CreateTestTodos;

class GetSingleTodoActionTest extends TestCase
{
    use CreateTestTodos;

    /**
     * Generate route for updating a todo by ID.
     */
    private function getRoute(int|string $todoId): string
    {
        return $this->generateRouteUrl('todos.show', ['id' => $todoId]);
    }

    /**
     * Test successful fetching of a single todo.
     */
    public function testGetSingleTodoActionSuccess(): void
    {
        $app = $this->getAppInstance();

        // Create a test todo
        $todo = $this->createTodo();

        // Make a GET request to fetch the todo
        $request = $this->createRequest('GET', $this->getRoute($todo->id));
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        // Assertions
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(200, $payload['statusCode']);
        $this->assertTrue($payload['data']['status']);
        $this->assertEquals(trans("todo::messages.todo_fetched"), $payload['data']['message']);
    }

    /**
     * Test fetching a non-existing todo.
     */
    public function testGetSingleTodoActionNotFound(): void
    {
        $app = $this->getAppInstance();

        // Make a GET request for a non-existing todo ID
        $request = $this->createRequest('GET', $this->getRoute(99999)); // Non-existing ID
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        // Assertions
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals(404, $payload['statusCode']);
        $this->assertFalse($payload['data']['status']);
        $this->assertEquals(trans("todo::messages.todo_not_found"), $payload['data']['message']);
    }

    /**
     * Test fetching with an invalid Todo ID format.
     */
    public function testGetSingleTodoActionInvalidId(): void
    {
        $app = $this->getAppInstance();

        // Make a GET request with an invalid ID format
        $request = $this->createRequest('GET', $this->getRoute('invalid-id')); // Invalid ID format
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        // Assertions
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals(404, $payload['statusCode']);
        $this->assertFalse($payload['data']['status']);
    }
}
