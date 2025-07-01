<?php

declare(strict_types=1);
namespace Modules\Todo\Tests;


use BitCore\Tests\TestCase as BaseTestCase;
use Modules\Todo\Repositories\Todos\TodoRepositoryInterface;
use Modules\Todo\Repositories\Groups\GroupRepositoryInterface;


// use Tests\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected TodoRepositoryInterface $todoRepository;
    protected GroupRepositoryInterface $groupRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $app = $this->getAppInstance();
        /** @var \BitCore\Foundation\Container $container */
        $container = $app->getContainer();
        
        // Initialize repositories
        $this->todoRepository = $container->get(TodoRepositoryInterface::class);
        $this->groupRepository = $container->get(GroupRepositoryInterface::class);
    }

    protected function tearDown(): void
    {
        // Clean up test data
       unset($this->todoRepository);
        unset($this->groupRepository);
        
        parent::tearDown();
    }
}
