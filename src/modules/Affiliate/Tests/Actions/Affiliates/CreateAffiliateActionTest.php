<?php

declare(strict_types=1);

namespace Modules\Affiliate\Tests\Actions\Affiliates;

use BitCore\Foundation\Carbon;
use Modules\Affiliate\Tests\TestCase;
use Modules\Affiliate\Tests\Traits\CreateTestAffiliates;

class CreateAffiliateActionTest extends TestCase
{
    use CreateTestAffiliates;

    /**
     * Generate route for updating a contract by ID.
     */
    private function getRoute(): string
    {
        return $this->generateRouteUrl('affiliates.store');
    }

    /**
     * Test successful creation of an affiliate.
     */
    public function testCreateAffiliateActionSuccess(): void
    {
        $app = $this->getAppInstance();

        $requestData = [
            'affiliate_name'   => 'John Doe ' . uniqid(),
            'affiliate_slug'   => 'john-doe-' . uniqid(),
            'referral_link'    => 'https://example.com/referral/' . uniqid(),
            'status'           => 'enabled',
            'clicks_generated' => 10,
            'earnings'         => 100.50,
            'payout_date'      => Carbon::now()->toIso8601String(),
            'payout_status'    => 'pending',
            'total_sales'      => 5,
            'commission'       => 10.0,
            'group_id'         => 1,
        ];

        $request = $this->createRequestWithCsrf($app, $this->getRoute(), 'POST', $requestData);
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);
        // var_dump($payload);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals(201, $payload['statusCode']);
        $this->assertArrayHasKey('status', $payload['data']);
        $this->assertTrue($payload['data']['status']);
        $this->assertEquals(trans("Affiliate::messages.affiliate_created"), $payload['data']['message']);
        $this->assertArrayHasKey('affiliate', $payload['data']);
        $this->assertNotEmpty($payload['data']['affiliate']);
    }

    /**
     * Test creation of an affiliate with missing required fields.
     */
    public function testCreateAffiliateValidationError(): void
    {
        $app = $this->getAppInstance();

        $requestData = [
            // missing affiliate_name, affiliate_slug, and other required fields
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
