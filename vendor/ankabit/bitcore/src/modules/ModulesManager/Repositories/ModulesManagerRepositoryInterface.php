<?php

declare(strict_types=1);

namespace BitCore\Modules\ModulesManager\Repositories;

use BitCore\Application\Repositories\AppRepositoryInterface;
use BitCore\Application\Services\Modules\ModuleInterface;
use BitCore\Modules\ModulesManager\Models\Module;

interface ModulesManagerRepositoryInterface extends AppRepositoryInterface
{
    /**
     * {@inheritDoc}
     * @return Module|null
     */
    public function findById(int $id);

    /**
     * {@inheritDoc}
     * @return Module The newly created module instance.
     */
    public function create(array $data);

    /**
     * {@inheritDoc}
     * @return Module The updated module.
     */
    public function update(int $id, array $data);

    /**
     * Find a module entry by its name.
     *
     * @param string $name The name of the module to find.
     * @return Module|null The module instance if found, or null if not found.
     */
    public function findByName(string $name): ?Module;

    /**
     * Install a module.
     *
     * @param string $zipFile The absolute path (local) to the achive file.
     * @throws Exception
     *
     * @return ModuleInterface
     */
    public function install(string $zipFile): ModuleInterface;

    /**
     * Uninstall a module.
     *
     * @param Module $module
     * @throws Exception
     */
    public function uninstall(Module $module): ModuleInterface;

    /**
     * Activate a module.
     *
     * @param Module $moduleId
     * @throws Exception
     *
     * @return ModuleInterface
     */
    public function activate(Module $module): ModuleInterface;

    /**
     * Deactivate a module.
     *
     * @param Module $module
     * @throws Exception
     */
    public function deactivate(Module $module): ModuleInterface;
}
