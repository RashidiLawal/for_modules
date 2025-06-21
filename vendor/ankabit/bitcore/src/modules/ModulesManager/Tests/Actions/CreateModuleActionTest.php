<?php

declare(strict_types=1);

namespace BitCore\Modules\ModulesManager\Tests\Actions;

use BitCore\Modules\ModulesManager\Tests\TestCase;
use BitCore\Modules\ModulesManager\Tests\Traits\CreateTestModules;

class CreateModuleActionTest extends TestCase
{
    use CreateTestModules;

    /**
     * Generate route for create source.
     */
    private function getRoute(): string
    {
        return $this->generateRouteUrl('modules.store');
    }
    /**
     * Test successful creation of a module.
     */
    public function testCreateModuleActionSuccess(): void
    {
        $app = $this->getAppInstance();

        $requestData = [
            'name'        => 'TestModule_' . uniqid(),
            // 'priority'    => 1,
            'entry'       => 'Modules/TestModule/index.php',
            'status'      => 'inactive',
            'type'        => 'custom',
            'plan'        => 'free',
            'description' => 'This is a test module.',
            // 'images'      => json_encode(['icon.png']),
        ];

        $request = $this->createRequestWithCsrf($this->app, $this->getRoute(), 'POST', $requestData);
        $response = $app->handle($request);

        $payload = json_decode((string) $response->getBody(), true);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertArrayHasKey('status', $payload['data']);
        $this->assertTrue($payload['data']['status']);
        $this->assertEquals(trans("ModulesManager::messages.module_created"), $payload['data']['message']);
        $this->assertArrayHasKey('data', $payload['data']);
        $this->assertNotEmpty($payload['data']['data']);
    }

    /**
     * Test module creation with missing required fields.
     */
    public function testCreateModuleValidationError(): void
    {
        $app = $this->getAppInstance();

        $requestData = [
            // Missing name, namespace, description, version
        ];

        $request = $this->createRequestWithCsrf($this->app, $this->getRoute(), 'POST', $requestData);
        $response = $app->handle($request);

        $payload = json_decode((string) $response->getBody(), true);

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertArrayHasKey('status', $payload['data']);
        $this->assertFalse($payload['data']['status']);
        $this->assertArrayHasKey('errors', $payload['data']);
    }
}
