<?php

namespace BitCore\Modules\ModulesManager\Services;

use BitCore\Application\Services\Modules\ModuleInterface;
use BitCore\Application\Services\Modules\ModuleRegistry;
use BitCore\Foundation\Filesystem\FilesystemInterface;
use BitCore\Foundation\Hooks\HookDispatcherInterface;
use BitCore\Modules\ModulesManager\Exceptions\ModuleNotFoundException;
use BitCore\Modules\ModulesManager\Exceptions\InvalidModuleActionException;
use BitCore\Modules\ModulesManager\Exceptions\ModuleUninstallException;
use BitCore\Modules\ModulesManager\Repositories\ModuleActionsTrait;

class ModuleUninstaller
{
    /**
     * The filesystem instance for managing module files and directories.
     *
     * @var FilesystemInterface
     */
    protected FilesystemInterface $storage;

    /**
     * The base directory for module storage.
     *
     * @var string
     */
    protected string $modulesDir;

    /**
     * The public directory for module assets.
     *
     * @var string
     */
    protected string $publicDirName;

    /**
     * Constructs a new ModuleUninstaller instance.
     *
     * @param FilesystemInterface $storage The filesystem interface for file operations.
     * @param ModuleRegistry $moduleRegistry The module registry for finding modules.
     * @param HookDispatcherInterface|null $hookDispatcher The hook dispatcher for events (optional).
     * @param string $modulesDir The base directory for modules (default: from get_module_upload_dir).
     * @param string $publicDirName The public directory for module assets (default: 'public').
     */
    public function __construct(
        FilesystemInterface $storage,
        string $modulesDir,
        string $publicDirName = 'public'
    ) {
        $this->storage = $storage;
        $this->modulesDir = $modulesDir ?: get_module_upload_dir();
        $this->publicDirName = $publicDirName;
    }

    /**
     * Uninstalls a module by executing its uninstall action and removing its files.
     *
     * Dispatches module.before.uninstall and module.after.uninstall events.
     * Deletes the module directory and public assets.
     *
     * @param string $moduleId The unique identifier of the module to uninstall.
     * @return bool The module instance after uninstallation.
     */
    public function process(string $moduleId): bool
    {

        $uninstalled = false;

        // Clean up module directory
        $moduleDir = $this->modulesDir . $moduleId;
        if ($moduleDir !== $this->modulesDir && $this->storage->exists($moduleDir)) {
            $uninstalled = $this->storage->deleteDirectory($moduleDir);
        }

        if (!$uninstalled) {
            return false;
        }

        // Clean up public assets
        $modulePublicDir = $this->publicDirName . '/' . basename($this->modulesDir) . '/' . $moduleId;
        if ($modulePublicDir !== $this->publicDirName && $this->storage->exists($modulePublicDir)) {
            $uninstalled = $this->storage->deleteDirectory($modulePublicDir);
        }

        return $uninstalled;
    }
}
