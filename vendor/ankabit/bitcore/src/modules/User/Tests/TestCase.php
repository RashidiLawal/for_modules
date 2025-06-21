<?php

declare(strict_types=1);

namespace BitCore\Modules\User\Tests;

use BitCore\Tests\TestCase as BaseTestCase;
use BitCore\Modules\User\Repositories\UserRepositoryInterface;

class TestCase extends BaseTestCase
{
    protected $userRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $app = $this->getAppInstance();
        /** @var \BitCore\Foundation\Container $container */
        $container = $app->getContainer();

        $this->userRepository = $container->get(UserRepositoryInterface::class);
    }

    protected function tearDown(): void
    {
        $this->userRepository = null;
    }
}
