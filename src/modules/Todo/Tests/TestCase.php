<?php

declare(strict_types=1);
namespace Modules\Todo\Tests;

// use BitCore\Tests\TestCase as BaseTestCase;


use Tests\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected $affiliateRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $app = $this->getAppInstance();
        /** @var \BitCore\Foundation\Container $container */
        $container = $app->getContainer();
    }

    protected function tearDown(): void
    {
        $this->affiliateRepository = null;
    }
}
