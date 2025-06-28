<?php

declare(strict_types=1);

namespace Modules\Todo\Tests\Actions\Groups;

use Modules\Todo\Tests\TestCase;
use Modules\Todo\Tests\Traits\CreateTestGroups;

class CreateGroupActionTest extends TestCase
{
    use CreateTestGroups;

    /**
     * Generate route for creating a group.
     */
    private function getRoute(): string
    {
        return $this->generateRouteUrl('groups.store');
    }

    /**
     * Test successful creation of a group.
     */
    public function testCreateGroupActionSuccess(): void
    {
        $app = $this->getAppInstance();

        $requestData = [
            'group_title'       => 'Test Group ' . uniqid(),
            'group_description' => 'Test description for group',
            'completed'         => false,
        ];

        $request = $this->createRequestWithCsrf($app, $this->getRoute(), 'POST', $requestData);
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals(201, $payload['statusCode']);
        $this->assertArrayHasKey('status', $payload['data']);
        $this->assertTrue($payload['data']['status']);
        $this->assertEquals(trans("Todo::messages.group_created"), $payload['data']['message']);
        $this->assertArrayHasKey('group', $payload['data']);
        $this->assertNotEmpty($payload['data']['group']);
    }

    /**
     * Test creation of a group with missing required fields.
     */
    public function testCreateGroupValidationError(): void
    {
        $app = $this->getAppInstance();

        $requestData = [
            // missing group_title and group_description
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

    /**
     * Test creation of a group with duplicate title.
     */
    public function testCreateGroupDuplicateTitleError(): void
    {
        $app = $this->getAppInstance();

        // Create first group
        $existingGroup = $this->createGroup();

        $requestData = [
            'group_title'       => $existingGroup->group_title,
            'group_description' => 'Another description',
            'completed'         => false,
        ];

        $request = $this->createRequestWithCsrf($app, $this->getRoute(), 'POST', $requestData);
        $response = $app->handle($request);

        $payload = json_decode((string)$response->getBody(), true);

        $this->assertEquals(409, $response->getStatusCode());
        $this->assertArrayHasKey('status', $payload['data']);
        $this->assertFalse($payload['data']['status']);
    }
}
