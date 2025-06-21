<?php

declare(strict_types=1);

namespace Modules\Affiliate\Tests\Actions;

use Modules\Affiliate\Models\Affiliate;
use Modules\Affiliate\Tests\TestCase;
use Modules\Affiliate\Tests\Traits\CreateTestAffiliates;

class DeleteAffiliateActionTest extends TestCase
{
    use CreateTestAffiliates;

    /**
     * Generate route for updating a affiliate by ID.
     */
    private function getRoute(int|string $affiliateId): string
    {
        return $this->generateRouteUrl('affiliates.delete', ['id' => $affiliateId]);
    }

    /**
     * Test successful deletion of an affiliate.
     */
    public function testDeleteAffiliateActionSuccess(): void
    {
        $app = $this->getAppInstance();

        // Create a test affiliate
        $affiliate = $this->createAffiliate();

        // Make a DELETE request to delete the affiliate
        $request = $this->createRequestWithCsrf(
            $app,
            $this->getRoute($affiliate->id),
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
     * Test deletion of a non-existing affiliate.
     */
    public function testDeleteAffiliateActionNotFound(): void
    {
        $app = $this->getAppInstance();

        // Make a DELETE request for a non-existing affiliate ID
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
