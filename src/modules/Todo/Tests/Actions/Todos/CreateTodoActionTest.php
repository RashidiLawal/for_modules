<?php

declare(strict_types=1);

namespace Modules\Todo\Tests\Actions\Todos;

use BitCore\Foundation\Carbon;
use Modules\Todo\Tests\TestCase;
use Modules\Todo\Tests\Traits\CreateTestTodos;
use function Aws\boolean_value;

class CreateTodoActionTest extends TestCase
{
    use CreateTestTodos;

    /**
     * Generate route for updating a contract by ID.
     */
    private function getRoute(): string
    {
        return $this->generateRouteUrl('todos.store');
    }

    /**
     * Test successful creation of an todo.
     */
    public function testCreateActionSuccess(): void
    {
        $app = $this->getAppInstance();

        $requestData = [
            'todo_id'   => 1,
            'todo_title'   => 'Dodo ' . uniqid(),
            'todo_description'   => 'fried plantain',
            'completed'   => true,
        ];

        $request = $this->createRequestWithCsrf($app, $this->getRoute(), 'POST', $requestData);
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);
        // var_dump($payload);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals(201, $payload['statusCode']);
        $this->assertArrayHasKey('status', $payload['data']);
        $this->assertTrue($payload['data']['status']);
        $this->assertEquals(trans("todo::messages.todo_created"), $payload['data']['message']);
        $this->assertArrayHasKey('todo', $payload['data']);
        $this->assertNotEmpty($payload['data']['todo']);
    }

    /**
     * Test creation of an todo with missing required fields.
     */
    public function testCreateTodoValidationError(): void
    {
        $app = $this->getAppInstance();

        $requestData = [
            // missing todo_title, todo_slug, and other required fields
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
}
