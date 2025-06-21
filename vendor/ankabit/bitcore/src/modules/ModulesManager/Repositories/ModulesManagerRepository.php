<?php

declare(strict_types=1);

namespace BitCore\Modules\ModulesManager\Repositories;

use BitCore\Application\Repositories\AppRepository;
use BitCore\Application\Services\Modules\ModuleActionsTrait;
use BitCore\Application\Services\Modules\ModuleInterface;
use BitCore\Application\Services\Modules\ModuleRegistry;
use BitCore\Modules\ModulesManager\Models\Module;
use BitCore\Modules\ModulesManager\Repositories\ModulesManagerRepositoryInterface;
use BitCore\Modules\ModulesManager\Services\ModuleInstaller;
use BitCore\Modules\ModulesManager\Services\ModuleUninstaller;

/**
 * Class ModulesManagerRepository
 *
 * Repository implementation for managing ModulesManager model records.
 * Provides methods to retrieve and update module information.
 *
 * @package BitCore\Modules\ModulesManager\Repositories
 *
 */
class ModulesManagerRepository extends AppRepository implements ModulesManagerRepositoryInterface
{
    use ModuleActionsTrait;

    protected string $modelClass = Module::class;

    protected ModuleRegistry $moduleRegistry;

    /**
     * BaseRepository constructor.
     *
     * @param string|null $modelClass The fully qualified class name of the model.
     *
     * @throws RuntimeException If no model class is provided.
     */
    public function __construct(ModuleRegistry $moduleRegistry)
    {
        $this->moduleRegistry = $moduleRegistry;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    public function findByName(string $name): ?Module
    {
        /** @var Module|null */
        $module = $this->findByColumn('name', $name);
        return $module;
    }

    /**
     * {@inheritDoc}
     */
    public function install(string $zipFile): ModuleInterface
    {
        $uploadedr = new ModuleInstaller(
            $zipFile,
            storage('system'),
            get_module_upload_dir(),
            container(ModuleRegistry::class)
        );

        $module = $uploadedr->process();

        return $this->handleModuleAction($module->getId(), 'install');
    }

    /**
     * {@inheritDoc}
     */
    public function activate(Module $module): ModuleInterface
    {
        return $this->handleModuleAction($module->name, 'activate', function () use ($module) {
            return $this->updateModel($module, ['status' => Module::STATUS_ACTIVE]);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function deactivate(Module $module): ModuleInterface
    {
        return $this->handleModuleAction($module->name, 'deactivate', function () use ($module) {
            return $this->updateModel($module, ['status' => Module::STATUS_INACTIVE]);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function uninstall(Module $module): ModuleInterface
    {
        return $this->handleModuleAction($module->name, 'uninstall', function () use ($module) {
            $uploadedr = new ModuleUninstaller(
                storage('system'),
                get_module_upload_dir()
            );
            $uploadedr->process($module->name);
            return $this->delete($module->id);
        });
    }
}
