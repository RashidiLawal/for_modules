<?php

declare(strict_types=1);

namespace Modules\Affiliate\Tests\Actions\Affiliates;

use Modules\Affiliate\Models\Affiliate;
use Modules\Affiliate\Tests\TestCase;
use Modules\Affiliate\Tests\Traits\CreateTestAffiliates;

class BulkDeleteAffiliateActionTest extends TestCase
{
    use CreateTestAffiliates;

    /**
    * Generate route for updating a contract by ID.
    */
    private function getRoute(): string
    {
        return $this->generateRouteUrl('affiliates.bulkDelete');
    }

    /**
     * Test successful bulk deletion of affiliates.
     */
    public function testBulkDeleteAffiliateActionSuccess(): void
    {
        $app = $this->getAppInstance();

        // Create test affiliates
        $affiliate1 = $this->createAffiliate();
        $affiliate2 = $this->createAffiliate();
        $affiliate3 = $this->createAffiliate();

        $requestData = [
            'ids' => [$affiliate1->id, $affiliate2->id, $affiliate3->id],
        ];

        // Make a DELETE request to bulk delete affiliates
        $request = $this->createRequestWithCsrf($app, $this->getRoute(), 'DELETE', $requestData);
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(200, $payload['statusCode']);
        $this->assertArrayHasKey('status', $payload['data']);
        $this->assertTrue($payload['data']['status']);
        $this->assertEquals(trans("Affiliate::messages.bulk_affiliate_delete_success"), $payload['data']['message']);
    }

    /**
     * Test bulk deletion with invalid or missing IDs.
     */
    public function testBulkDeleteAffiliateValidationError(): void
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
