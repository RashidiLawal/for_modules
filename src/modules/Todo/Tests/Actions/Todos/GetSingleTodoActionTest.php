<?php

declare(strict_types=1);

namespace Modules\Todo\Tests\Actions\Todos;

use Modules\Todo\Tests\TestCase;
use Modules\Todo\Tests\Traits\CreateTestTodos;

class GetSingleTodoActionTest extends TestCase
{
    use CreateTestTodos;

    /**
     * Generate route for getting a single todo by ID.
     */
    private function getRoute(int $id): string
    {
        return $this->generateRouteUrl('todos.show', ['id' => $id]);
    }

    /**
     * Test successful retrieval of a single todo.
     */
    public function testGetSingleTodoActionSuccess(): void
    {
        $app = $this->getAppInstance();

        // Create a todo to retrieve
        $todo = $this->createTodo();

        $request = $this->createRequest('GET', $this->getRoute($todo->id));
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(200, $payload['statusCode']);
        $this->assertArrayHasKey('status', $payload['data']);
        $this->assertTrue($payload['data']['status']);
        $this->assertEquals(trans("Todo::messages.fetch_success"), $payload['data']['message']);
        $this->assertArrayHasKey('todo', $payload['data']);
        $this->assertEquals($todo->id, $payload['data']['todo']['id']);
        $this->assertEquals($todo->todo_title, $payload['data']['todo']['todo_title']);
    }

    /**
     * Test retrieval of a non-existent todo.
     */
    public function testGetSingleTodoActionNotFound(): void
    {
        $app = $this->getAppInstance();

        $request = $this->createRequest('GET', $this->getRoute(99999));
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertArrayHasKey('status', $payload['data']);
        $this->assertFalse($payload['data']['status']);
        $this->assertEquals(trans("Todo::messages.todo_not_found"), $payload['data']['message']);
    }

    /**
     * Test retrieval with invalid ID format.
     */
    public function testGetSingleTodoActionInvalidId(): void
    {
        $app = $this->getAppInstance();

        $request = $this->createRequest('GET', $this->getRoute(0));
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertArrayHasKey('status', $payload['data']);
        $this->assertFalse($payload['data']['status']);
    }
}
