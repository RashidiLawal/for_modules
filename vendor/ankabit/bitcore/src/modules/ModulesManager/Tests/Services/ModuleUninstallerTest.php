<?php

namespace BitCore\Modules\ModulesManager\Tests\Services;

use BitCore\Application\Services\Modules\ModuleInterface;
use BitCore\Modules\ModulesManager\Services\ModuleUploader;
use BitCore\Modules\ModulesManager\Services\ModuleUninstaller;
use BitCore\Application\Services\Modules\ModuleRegistry;
use BitCore\Foundation\Filesystem\FilesystemInterface;
use BitCore\Modules\ModulesManager\Services\ModuleInstaller;
use BitCore\Modules\ModulesManager\Tests\TestCase;
use ZipArchive;
use Exception;

class ModuleUninstallerTest extends TestCase
{
    protected $storage;
    protected $moduleRegistry;
    protected $tempBaseDir;
    protected $zipPath;
    protected $modulesDir;
    protected $moduleName;
    protected $publicDirName = 'public';

    protected function setUp(): void
    {
        parent::setUp();

        // Derive storage and module registry
        $this->storage = storage('system');
        $this->moduleRegistry = container(ModuleRegistry::class);

        // Create temporary base directory
        $this->tempBaseDir = 'test_temp_' . time();
        $this->storage->makeDirectory($this->tempBaseDir);

        // Set up paths
        $this->zipPath = $this->tempBaseDir . '/module.zip';
        $this->modulesDir = get_module_upload_dir();
        $this->storage->makeDirectory($this->modulesDir);

        // Unique module name
        $this->moduleName = 'TestModule' . time();

        // Ensure container is set for ModuleUploader and ModuleUninstaller
        if (!isset($GLOBALS['container'])) {
            $GLOBALS['container'] = new class
            {
                protected $instances = [];
                public function bind($abstract, $concrete)
                {
                    $this->instances[$abstract] = $concrete;
                }
                public function make($abstract)
                {
                    return $this->instances[$abstract] ?? null;
                }
            };
        }
        $GLOBALS['container']->bind(ModuleRegistry::class, $this->moduleRegistry);
    }

    protected function tearDown(): void
    {
        // Clean up temporary directory
        if ($this->storage->exists($this->tempBaseDir)) {
            $this->storage->deleteDirectory($this->tempBaseDir);
        }

        // Uninstall the module
        $moduleDir = $this->modulesDir . $this->moduleName;
        if ($moduleDir !== $this->modulesDir && $this->storage->exists($moduleDir)) {
            $this->storage->deleteDirectory($moduleDir);
        }

        $modulePublicDir = $this->publicDirName .  '/' . basename($this->modulesDir) . '/'  . $this->moduleName;
        if ($modulePublicDir !== $this->publicDirName && $this->storage->exists($modulePublicDir)) {
            $this->storage->deleteDirectory($modulePublicDir);
        }

        parent::tearDown();
    }

    /**
     * Create a ZIP file with specified structure.
     */
    protected function createZipFile(string $zipPath, array $structure): void
    {
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new Exception("Cannot create ZIP file at $zipPath");
        }

        foreach ($structure as $file => $content) {
            $zip->addFromString($file, $content);
        }

        $zip->close();
    }

    /**
     * Generate a valid module entry file content with uninstall method.
     */
    protected function getValidModuleEntryFile(string $moduleName, bool $isCore = false): string
    {
        $isCore = $isCore ? 'true' : 'false';

        return <<<PHP
<?php

declare(strict_types=1);

namespace Bitcore\\Modules\\{$moduleName};

use BitCore\Application\Services\Modules\AbstractModule;

class {$moduleName} extends AbstractModule
{
    protected \$id = '{$moduleName}';
    protected \$name = 'Test Module';
    protected \$description = 'Sample test module.';
    protected \$version = '1.0.0';
    protected \$autoloadRoute = false;
    protected \$autoloadRegister = false;
    protected \$autoloadBoot = false;
    protected \$priority = 1;
    protected \$isCore = {$isCore};

    public function uninstall(): void
    {
        // Simulate uninstall logic
    }
}
PHP;
    }

    /**
     * Install a module for testing uninstallation.
     */
    protected function installModule(string $moduleName): ?ModuleInterface
    {
        // Create a valid module ZIP
        $zipStructure = [
            "{$moduleName}/{$moduleName}.php" => $this->getValidModuleEntryFile($moduleName),
            "{$moduleName}/public/style.css" => 'body { color: black; }',
        ];
        $this->createZipFile($this->storage->path($this->zipPath), $zipStructure);

        $uploader = new ModuleInstaller(
            $this->zipPath,
            $this->storage,
            $this->modulesDir,
            $this->moduleRegistry,
            $this->publicDirName,
            tempDirName: $this->tempBaseDir
        );

        return $uploader->process();
    }

    public function testUninstallSuccess()
    {
        // Install module
        $module = $this->installModule($this->moduleName);
        $this->moduleRegistry->loadModules([$this->moduleName]);

        // Verify module exists
        $moduleDir = $this->modulesDir . $this->moduleName;
        $publicDir = $this->publicDirName . '/' . basename($this->modulesDir) . '/' . $this->moduleName;
        $this->assertFileExists($this->storage->path($moduleDir . '/' . $this->moduleName . '.php'));
        $this->assertFileExists($this->storage->path($publicDir . '/style.css'));

        // Create uninstaller
        $uninstaller = new ModuleUninstaller(
            $this->storage,
            $this->modulesDir,
            $this->publicDirName
        );

        // Uninstall module
        $result = $uninstaller->process($this->moduleName);

        // Verify results
        $this->assertInstanceOf(\BitCore\Application\Services\Modules\ModuleInterface::class, $result);
        $this->assertTrue($result);
        $this->assertDirectoryDoesNotExist($this->storage->path($moduleDir));
        $this->assertDirectoryDoesNotExist($this->storage->path($publicDir));
    }

    public function testUninstallFailedWhenModuleNotFound()
    {
        $uninstaller = new ModuleUninstaller(
            $this->storage,
            $this->modulesDir,
            $this->publicDirName
        );


        $uninstall = $uninstaller->process('NonExistentModule');

        $this->assertFalse($uninstall);
    }
}
