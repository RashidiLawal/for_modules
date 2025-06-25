<?php

declare(strict_types=1);

namespace Modules\Todo\Tests\Actions\Todos;

use Modules\Todo\Models\Todo;
use Modules\Todo\Tests\TestCase;
use Modules\Todo\Tests\Traits\CreateTestTodos;

class BulkDeleteTodoActionTest extends TestCase
{
    use CreateTestTodos;

    /**
    * Generate route for updating a contract by ID.
    */
    private function getRoute(): string
    {
        return $this->generateRouteUrl('todos.bulkDelete');
    }

    /**
     * Test successful bulk deletion of todos.
     */
    public function testBulkDeleteTodoActionSuccess(): void
    z
    {
        $app = $this->getAppInstance();

        // Create test todos
        $todo1 = $this->createTodo();
        $todo2 = $this->createTodo();
        $todo3 = $this->createTodo();

        $requestData = [
            'ids' => [$todo1->id, $todo2->id, $todo3->id],
        ];

        // Make a DELETE request to bulk delete todos
        $request = $this->createRequestWithCsrf($app, $this->getRoute(), 'DELETE', $requestData);
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(200, $payload['statusCode']);
        $this->assertArrayHasKey('status', $payload['data']);
        $this->assertTrue($payload['data']['status']);
        $this->assertEquals(trans("Todos::messages.bulk_todo_delete_success"), $payload['data']['message']);
    }

    /**
     * Test bulk deletion with invalid or missing IDs.
     */
    public function testBulkDeleteTodoValidationError(): void
    {
        $app = $this->getAppInstance();

        $requestData = [
            'id' => [], // Empty IDs array
        ];

        // Make a DELETE request with invalid data
        $request = $this->createRequestWithCsrf($app, $this->getRoute(), 'DELETE', $requestData);
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals(422, $payload['statusCode']);
        $this->assertArrayHasKey('status', $payload['data']);
        $this->assertFalse($payload['data']['status']);
    }
}
