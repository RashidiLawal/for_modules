<?php

declare(strict_types=1);

namespace Modules\Todo\Tests\Actions\Todos;

use Modules\Todo\Models\Todo;
use Modules\Todo\Tests\TestCase;
use Modules\Todo\Tests\Traits\CreateTestTodos;

class ListAllTodoActionTest extends TestCase
{
    use CreateTestTodos;

    /**
     * Generate route for fetching todos with optional query parameters.
     *
     * @param array $queryParams Associative array of query parameters.
     * @return string The generated route URL.
     */
    private function getRoute(array $queryParams = []): string
    {
        return $this->generateRouteUrl('todos.index', [], $queryParams);
    }

    /**
     * Test successful fetching of all todos with pagination.
     */
    public function testFetchAllTodoActionSuccess(): void
    {
        $app = $this->getAppInstance();

        // Create test todos
        $this->createTodo(['todo_title' => 'Todo 1']);
        $this->createTodo(['todo_title' => 'Todo 2']);
        $this->createTodo(['todo_title' => 'Todo 3']);

        // Make a GET request to fetch all todos
        $request = $this->createRequest('GET', $this->getRoute([]));
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(200, $payload['statusCode']);
        $this->assertTrue($payload['data']['status']);
        $this->assertEquals(trans("Todo::messages.fetch_success"), $payload['data']['message']);
        $this->assertArrayHasKey('todos', $payload['data']);
    }

    /**
     * Test fetching todos with filters.
     */
    public function testFetchAllTodoActionWithFilters(): void
    {
        $app = $this->getAppInstance();

        // Create test todos
        $this->createTodo(['todo_title' => 'Todo A']);
        $this->createTodo(['todo_title' => 'Todo B']);
        $this->createTodo(['todo_title' => 'Todo C']);

        // Make a GET request with a filter
        $request = $this->createGetRequest($this->getRoute(), ['filters' => ['todo_title' => 'Todo A']]);
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(200, $payload['statusCode']);
        $this->assertTrue($payload['data']['status']);
        $this->assertEquals(trans("Todo::messages.fetch_success"), $payload['data']['message']);
        $this->assertArrayHasKey('todos', $payload['data']);
    }

    /**
     * Test fetching todos with sorting.
     */
    public function testFetchAllTodoActionWithSorting(): void
    {
        $app = $this->getAppInstance();

        // Create test todos
        $this->createTodo(['todo_title' => 'Todo Z']);
        $this->createTodo(['todo_title' => 'Todo A']);
        $this->createTodo(['todo_title' => 'Todo M']);

        // Make a GET request with sorting
        $request = $this->createGetRequest($this->getRoute(), [
            'sort_by' => 'todo_title',
            'sort_order' => 'ASC'
        ]);
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(200, $payload['statusCode']);
        $this->assertTrue($payload['data']['status']);
        $this->assertArrayHasKey('todos', $payload['data']);
    }

    /**
     * Test fetching todos with pagination.
     */
    public function testFetchAllTodoActionWithPagination(): void
    {
        $app = $this->getAppInstance();

        // Create test todos
        for ($i = 1; $i <= 5; $i++) {
            $this->createTodo(['todo_title' => "Todo {$i}"]);
        }

        // Make a GET request with pagination
        $request = $this->createGetRequest($this->getRoute(), [
            'page' => 1,
            'per_page' => 2
        ]);
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($payload['data']['status']);
        $this->assertArrayHasKey('todos', $payload['data']);
        $this->assertArrayHasKey('pagination', $payload['data']);
    }
}
