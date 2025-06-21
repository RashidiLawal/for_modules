<?php

declare(strict_types=1);

namespace Modules\Affiliate\Tests\Actions;

use Modules\Affiliate\Models\Affiliate;
use Modules\Affiliate\Tests\TestCase;
use Modules\Affiliate\Tests\Traits\CreateTestAffiliates;

class GetSingleAffiliateActionTest extends TestCase
{
    use CreateTestAffiliates;

    /**
     * Generate route for updating a affiliate by ID.
     */
    private function getRoute(int|string $affiliateId): string
    {
        return $this->generateRouteUrl('affiliates.show', ['id' => $affiliateId]);
    }

    /**
     * Test successful fetching of a single affiliate.
     */
    public function testGetSingleAffiliateActionSuccess(): void
    {
        $app = $this->getAppInstance();

        // Create a test affiliate
        $affiliate = $this->createAffiliate();

        // Make a GET request to fetch the affiliate
        $request = $this->createRequest('GET', $this->getRoute($affiliate->id));
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        // Assertions
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(200, $payload['statusCode']);
        $this->assertTrue($payload['data']['status']);
        $this->assertEquals(trans("Affiliate::messages.affiliate_fetched"), $payload['data']['message']);
    }

    /**
     * Test fetching a non-existing affiliate.
     */
    public function testGetSingleAffiliateActionNotFound(): void
    {
        $app = $this->getAppInstance();

        // Make a GET request for a non-existing affiliate ID
        $request = $this->createRequest('GET', $this->getRoute(99999)); // Non-existing ID
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        // Assertions
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals(404, $payload['statusCode']);
        $this->assertFalse($payload['data']['status']);
        $this->assertEquals(trans("Affiliate::messages.affiliate_not_found"), $payload['data']['message']);
    }

    /**
     * Test fetching with an invalid affiliate ID format.
     */
    public function testGetSingleAffiliateActionInvalidId(): void
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
