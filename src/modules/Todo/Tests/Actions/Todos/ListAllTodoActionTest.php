<?php

declare(strict_types=1);

namespace Modules\Todo\Tests\Actions;

use Modules\Todo\Models\Todo;
use Modules\Todo\Tests\TestCase;
use Modules\Todo\Tests\Traits\CreateTestTodos;


class ListAllTodoActionTest extends TestCase
{
    use CreateTestTodos;

    /**
     * Generate route for fetching todo with optional query parameters.
     *
     * @param array $queryParams Associative array of query parameters.
     * @return string The generated route URL.
     */
    private function getRoute(array $queryParams = []): string
    {
        return $this->generateRouteUrl('affiliates.index', [], $queryParams);
    }

    /**
     * Test successful fetching of all todos with pagination.
     */
    public function testFetchAllAffiliateActionSuccess(): void
    {
        $app = $this->getAppInstance();

        // Create test todos
        $this->createTodo(['affiliate_name' => 'Affiliate 1']);
        $this->createTodo(['affiliate_name' => 'Affiliate 2']);
        $this->createTodo(['affiliate_name' => 'Affiliate 3']);

        // Make a GET request to fetch all todos
        $request = $this->createRequest('GET', $this->getRoute([]));
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        // Assertions
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(200, $payload['statusCode']);
        $this->assertTrue($payload['data']['status']);
        $this->assertEquals(trans("Affiliate::messages.fetch_success"), $payload['data']['message']);
    }

    /**
     * Test fetching todos with filters.
     */
    
    public function testFetchAllTodoActionWithFilters(): void
    {
        $app = $this->getAppInstance();

        // Create test todos
        $this->createTodo(['affiliate_name' => 'Affiliate A']);
        $this->createTodo(['affiliate_name' => 'Affiliate B']);
        $this->createTodo(['affiliate_name' => 'Affiliate C']);

        // Make a GET request with a filter
        $request = $this->createGetRequest($this->getRoute(), ['filters' => ['affiliate_name' => 'Affiliate A']]);
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        // Assertions
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(200, $payload['statusCode']);
        $this->assertTrue($payload['data']['status']);
        $this->assertEquals(trans("Affiliate::messages.fetch_success"), $payload['data']['message']);
        $this->assertArrayHasKey('affiliates', $payload['data']);
    }

    /**
     * Test fetching todos with sorting.
     */
    public function testFetchAllAffiliateActionWithSorting(): void
    {
        $app = $this->getAppInstance();

        // Create test affiliates
        $this->createTodo(['affiliate_name' => 'Affiliate Z']);
        $this->createTodo(['affiliate_name' => 'Affiliate A']);
        $this->createTodo(['affiliate_name' => 'Affiliate M']);

        // Make a GET request with sorting
        $request = $this->createGetRequest($this->getRoute(), [
            'sort_by' => 'affiliate_name',
            'sort_order' => 'ASC'
        ]);
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        // Assertions
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(200, $payload['statusCode']);
        $this->assertTrue($payload['data']['status']);
    }
}
