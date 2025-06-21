<?php

declare(strict_types=1);

namespace Modules\Todo\Tests\Actions;

use Modules\Todo\Models\Todo;
use Modules\Todo\Tests\TestCase;
use Modules\Todo\Tests\Traits\CreateTestTodos;

class DeleteTodoActionTest extends TestCase
{
    use CreateTestTodos;

    /**
     * Generate route for updating a todo by ID.
     */
    private function getRoute(int|string $affiliateId): string
    {
        return $this->generateRouteUrl('todos.delete', ['id' => $affiliateId]);
    }

    /**
     * Test successful deletion of an todo.
     */
    public function testDeleteTodoActionSuccess(): void
    {
        $app = $this->getAppInstance();

        // Create a test todo
        $affiliate = $this->createTodo();

        // Make a DELETE request to delete the todo
        $request = $this->createRequestWithCsrf(
            $app,
            $this->getRoute($todo->id),
            'DELETE'
        );
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        // Assertions
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(200, $payload['statusCode']);
        $this->assertTrue($payload['data']['status']);
        $this->assertEquals(trans("Affiliate::messages.affiliate_deleted"), $payload['data']['message']);
    }

    /**
     * Test deletion of a non-existing todo.
     */
    public function testDeleteTodoActionNotFound(): void
    {
        $app = $this->getAppInstance();

        // Make a DELETE request for a non-existing todo ID
        $request = $this->createRequestWithCsrf(
            $app,
            $this->getRoute(9999), // Non-existing ID
            'DELETE'
        );
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        // Assertions
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals(404, $payload['statusCode']);
        $this->assertFalse($payload['data']['status']);
        $this->assertEquals(trans("Affiliate::messages.affiliate_not_found"), $payload['data']['message']);
    }
}
