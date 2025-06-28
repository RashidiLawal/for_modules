<?php

declare(strict_types=1);

namespace Modules\Todo\Tests\Actions\Todos;

use Modules\Todo\Tests\TestCase;
use Modules\Todo\Tests\Traits\CreateTestTodos;

class DeleteTodoActionTest extends TestCase
{
    use CreateTestTodos;

    /**
     * Generate route for deleting a todo by ID.
     */
    private function getRoute(int $id): string
    {
        return $this->generateRouteUrl('todos.delete', ['id' => $id]);
    }

    /**
     * Test successful deletion of a todo.
     */
    public function testDeleteTodoActionSuccess(): void
    {
        $app = $this->getAppInstance();

        // Create a todo to delete
        $todo = $this->createTodo();

        $request = $this->createRequestWithCsrf($app, $this->getRoute($todo->id), 'DELETE');
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(200, $payload['statusCode']);
        $this->assertArrayHasKey('status', $payload['data']);
        $this->assertTrue($payload['data']['status']);
        $this->assertEquals(trans("Todo::messages.todo_deleted"), $payload['data']['message']);
    }

    /**
     * Test deletion of a non-existent todo.
     */
    public function testDeleteTodoActionNotFound(): void
    {
        $app = $this->getAppInstance();

        $request = $this->createRequestWithCsrf($app, $this->getRoute(99999), 'DELETE');
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertArrayHasKey('status', $payload['data']);
        $this->assertFalse($payload['data']['status']);
    }

    /**
     * Test deletion with invalid ID format.
     */
    public function testDeleteTodoActionInvalidId(): void
    {
        $app = $this->getAppInstance();

        $request = $this->createRequestWithCsrf($app, $this->getRoute(0), 'DELETE');
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertArrayHasKey('status', $payload['data']);
        $this->assertFalse($payload['data']['status']);
    }
}
