<?php

declare(strict_types=1);

namespace Modules\Todo\Tests\Actions;

use Modules\Todo\Models\Todo;
use Modules\Todo\Tests\TestCase;
use Modules\Todo\Tests\Traits\CreateTestTodos;

class UpdateTodoActionTest extends TestCase
{
    use CreateTestTodos;

    /**
     * Generate route for updating a todo by ID.
     */
    private function getRoute(int|string $todo): string
    {
        return $this->generateRouteUrl('todos.update', ['id' => $todo]);
    }

    /**
     * Test successful editing of an todo.
     */
    public function testEditAffiliateActionSuccess(): void
    {

        $app = $this->getAppInstance();

        // Create a test todo
        $todo = $this->createTodo();

        $requestData = [
            'todo_title'     =>  'Updated Title',
            'todo_slug'     =>  'updated_title',
            'status'           =>  'enabled',
        ];

        // Make a PUT request to edit the todo
        $request = $this->createRequestWithCsrf(
            $app,
            $this->getRoute($todo->id),
            'PUT',
            $requestData
        );
        // Handle the request
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        // Assertions
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(200, $payload['statusCode']);
        $this->assertTrue($payload['data']['status']);
        $this->assertEquals(trans("Todo::messages.todo_updated"), $payload['data']['message']);
    }

    /**
     * Test editing with invalid data.
     */
    public function testEditAffiliateActionValidationError(): void
    {
        $app = $this->getAppInstance();

        // Create a test todo
        $affiliate = $this->createTodo();

        $requestData = [
            'todo_title' => '', // Invalid name (empty)
        ];

        // Make a PUT request with invalid data
        $request = $this->createRequestWithCsrf(
            $app,
            $this->getRoute($affiliate->id),
            'PUT',
            $requestData
        );
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        // Assertions
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals(422, $payload['statusCode']);
        $this->assertFalse($payload['data']['status']);
        $this->assertArrayHasKey('errors', $payload['data']);
        $this->assertNotEmpty($payload['data']['errors']);
    }
}
