<?php

declare(strict_types=1);

namespace BitCore\Modules\ModulesManager\Tests;

use BitCore\Tests\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected $modulesManagerRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $app = $this->getAppInstance();
        /** @var \BitCore\Foundation\Container $container */
        $container = $app->getContainer();
    }

    protected function tearDown(): void
    {
        $this->modulesManagerRepository = null;
    }
}
