<?php

declare(strict_types=1);

namespace BitCore\Modules\ModulesManager\Tests\Actions;

use BitCore\Modules\ModulesManager\Tests\TestCase;
use BitCore\Modules\ModulesManager\Tests\Traits\CreateTestModules;

class BulkDeleteModulesActionTest extends TestCase
{
    use CreateTestModules;

    /**
     * Generate route for create source.
     */
    private function getRoute(): string
    {
        return $this->generateRouteUrl('modules.bulkDelete');
    }

    public function testBulkDeleteModulesSuccess(): void
    {
        $app = $this->getAppInstance();

        $module1 = $this->createModule(['name' => 'TestModule1', 'type'  => 'custom']);
        $module2 = $this->createModule(['name' => 'TestModule2', 'type'  => 'custom']);
        $module3 = $this->createModule(['name' => 'TestModule3', 'type'  => 'custom']);

        $requestData = [
            'module_ids' => [$module1->id, $module2->id, $module3->id],
        ];

        $request = $this->createRequestWithCsrf(
            $this->app,
            $this->getRoute(),
            'DELETE',
            $requestData
        );
        $response = $app->handle($request);

        $payload = json_decode((string) $response->getBody(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($payload['data']['status']);
        $this->assertEquals(trans("ModulesManager::messages.bulk_modules_delete_success"), $payload['data']['message']);
    }

    public function testBulkDeleteModulesWithInvalidIdList(): void
    {
        $app = $this->getAppInstance();

        $requestData = [
            'module_ids' => 'invalid', // Should be array
        ];

        $request = $this->createRequestWithCsrf(
            $this->app,
            $this->getRoute(),
            'DELETE',
            $requestData
        );
        $response = $app->handle($request);

        $payload = json_decode((string) $response->getBody(), true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertFalse($payload['data']['status']);
    }

    public function testBulkDeleteModulesWithMissingIdList(): void
    {
        $app = $this->getAppInstance();

        $requestData = []; // No module_ids

        $request = $this->createRequestWithCsrf(
            $this->app,
            $this->getRoute(),
            'DELETE',
            $requestData
        );
        $response = $app->handle($request);

        $payload = json_decode((string) $response->getBody(), true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertFalse($payload['data']['status']);
    }
}
