<?php

declare(strict_types=1);
namespace Modules\Backup\Tests;

use BitCore\Tests\TestCase as BaseTestCase;
use Modules\Backup\Repositories\BackupRepositoryInterface;

/**
 * Base TestCase for Backup module tests.
 *
 * Extends BitCore's BaseTestCase and sets up the backup repository for use in tests.
 * Add Traits here for test utilities (e.g., CreateTestBackups).
 */
class TestCase extends BaseTestCase
{
    /**
     * The backup repository instance for use in tests.
     * @var BackupRepositoryInterface
     */
    protected BackupRepositoryInterface $backupRepository;

    /**
     * Set up the test environment and initialize the repository.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $app = $this->getAppInstance();
        /** @var \BitCore\Foundation\Container $container */
        $container = $app->getContainer();
        $this->backupRepository = $container->get(BackupRepositoryInterface::class);
    }

    /**
     * Clean up after the test.
     */
    protected function tearDown(): void
    {
        unset($this->backupRepository);
        parent::tearDown();
    }
} 