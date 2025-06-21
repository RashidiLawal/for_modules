<?php

namespace BitCore\Application\Services\Modules;

use BitCore\Foundation\Database\Manager as Capsule;

/**
 * Trait MigrationTrait
 *
 * This trait provides methods for running and rolling back migrations, and tracking versions and status.
 */
trait MigrationTrait
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function migrate(): void
    {
        $migrator = $this->getMigrator();
        $migrator->run($this->getMigrationPaths());
    }

    /**
     * Rollback the migrations.
     *
     * @param int $steps
     * @return void
     */
    public function rollback(int $steps = 1): void
    {
        $migrator = $this->getMigrator();
        $migrator->rollback($this->getMigrationPaths(), ['step' => $steps]);
    }

    /**
     * Get migrator instance.
     *
     * @return \BitCore\Foundation\Database\Migrator
     */
    protected function getMigrator()
    {
        return $this->container->get('migrator');
    }

    /**
     * Get paths to the migration files.
     *
     * @return array
     */
    protected function getMigrationPaths(): array
    {
        return [__DIR__ . '/Migrations'];
    }


    protected function migrationFolder(): string
    {
        return  __DIR__ . 'Migrations/';
    }

    protected function migrationTable()
    {
        /** @var Capsule $db */
        $db = $this->container->get('db');

        return $db->table('modules');
    }

    /**
     * Enable the module.
     *
     * @return bool
     */
    public function enable(): bool
    {
        return $this->migrationTable()->where('module', $this->getId())->update(['enabled' => true]) > 0;
    }

    /**
     * Disable the module.
     *
     * @return bool
     */
    public function disable(): bool
    {
        return $this->migrationTable()->where('module', $this->getId())->update(['enabled' => false]) > 0;
    }
}
