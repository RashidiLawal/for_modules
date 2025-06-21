<?php

declare(strict_types=1);

namespace BitCore\Modules\ModulesManager\Tests\Services;

use BitCore\Modules\ModulesManager\Services\ModuleInstaller;
use BitCore\Application\Services\Modules\ModuleRegistry;
use BitCore\Modules\ModulesManager\Tests\TestCase;
use ZipArchive;
use Exception;

class ModuleInstallerTest extends TestCase
{
    protected $storage;
    protected $moduleRegistry;
    protected $tempBaseDir;
    protected $zipPath;
    protected $modulesDir;
    protected $moduleName = 'TestModule';
    protected $publicDirName = 'public';

    protected function setUp(): void
    {
        parent::setUp();

        // Derive storage and module manager
        $this->storage = storage('system');
        $this->moduleRegistry = container(ModuleRegistry::class);

        $uid = bin2hex(random_bytes(8));

        // Create temporary base directory
        $this->tempBaseDir = 'test_temp_' . $uid;
        $this->storage->makeDirectory($this->tempBaseDir);

        // Set up paths
        $this->zipPath = $this->tempBaseDir . '/module.zip';
        $this->modulesDir = get_module_upload_dir();
        $this->storage->makeDirectory($this->modulesDir);
        $this->moduleName = 'TestModule' . $uid;
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

        $modulePublicDir = $this->publicDirName . '/' . basename($this->modulesDir) . '/' . $this->moduleName;
        if ($modulePublicDir !== $this->publicDirName && $this->storage->exists($modulePublicDir)) {
            $this->storage->deleteDirectory($modulePublicDir);
        }

        parent::tearDown();
    }

    protected function createModuleInstaller()
    {
        return new ModuleInstaller(
            $this->zipPath,
            $this->storage,
            $this->modulesDir,
            $this->moduleRegistry,
            $this->publicDirName,
            tempDirName: $this->tempBaseDir
        );
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
     * Generate a valid module entry file content.
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
}
PHP;
    }

    public function testProcessSuccessfulModuleUpload()
    {
        // Create a valid module ZIP
        $zipStructure = [
            "{$this->moduleName}/{$this->moduleName}.php" => $this->getValidModuleEntryFile($this->moduleName),
            "{$this->moduleName}/public/style.css" => 'body { color: black; }',
            "{$this->moduleName}/public/{$this->moduleName}.js" => "console.log('{$this->moduleName}')",
        ];
        $this->createZipFile($this->storage->path($this->zipPath), $zipStructure);

        $uploader = $this->createModuleInstaller();

        $result = $uploader->process();

        $this->assertInstanceOf(\BitCore\Application\Services\Modules\ModuleInterface::class, $result);
        $this->assertEquals($this->moduleName, $result->getId());
        $this->assertFileExists(
            $this->storage->path(
                $this->modulesDir . $this->moduleName . '/' . $this->moduleName . '.php'
            )
        );
        $this->assertFileExists(
            $this->storage->path(
                $this->publicDirName . '/' . basename($this->modulesDir) . '/' . $this->moduleName . '/style.css'
            )
        );
    }

    public function testExtractToTempThrowsExceptionOnInvalidZip()
    {
        // Create an invalid ZIP file
        $this->storage->put($this->zipPath, 'not a zip file');

        $uploader = $this->createModuleInstaller();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('modules.upload_error_open_zip');

        $method = new \ReflectionMethod(ModuleInstaller::class, 'extractToTemp');
        $method->setAccessible(true);
        $method->invoke($uploader);
    }

    public function testValidateSingleFolderThrowsExceptionOnMultipleFolders()
    {
        // Create a ZIP with multiple folders
        $zipStructure = [
            'Folder1/file.txt' => 'test',
            'Folder2/file.txt' => 'test',
        ];
        $this->createZipFile($this->storage->path($this->zipPath), $zipStructure);

        $uploader = $this->createModuleInstaller();

        // Extract manually
        $method = new \ReflectionMethod(ModuleInstaller::class, 'extractToTemp');
        $method->setAccessible(true);
        $method->invoke($uploader);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('modules.upload_error_multiple_folders');

        $method = new \ReflectionMethod(ModuleInstaller::class, 'validateSingleFolder');
        $method->setAccessible(true);
        $method->invoke($uploader);
    }

    public function testCheckEntryFileThrowsExceptionWhenNoPhpEntryFileExists()
    {
        // Create a ZIP with no PHP entry file
        $zipStructure = [
            "{$this->moduleName}/config.txt" => 'test',
        ];
        $this->createZipFile($this->storage->path($this->zipPath), $zipStructure);

        $uploader = $this->createModuleInstaller();

        // Extract and validate
        $method = new \ReflectionMethod(ModuleInstaller::class, 'extractToTemp');
        $method->setAccessible(true);
        $method->invoke($uploader);
        $method = new \ReflectionMethod(ModuleInstaller::class, 'validateSingleFolder');
        $method->setAccessible(true);
        $method->invoke($uploader);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('modules.upload_error_missing_entry');

        $method = new \ReflectionMethod(ModuleInstaller::class, 'checkEntryFile');
        $method->setAccessible(true);
        $method->invoke($uploader);
    }

    public function testCheckEntryFileThrowsExceptionWhenPublicFolderExistsButJsEntryFileMissing()
    {
        // Create a ZIP with a PHP entry file and a public folder but no JS entry file
        $zipStructure = [
            "{$this->moduleName}/{$this->moduleName}.php" => $this->getValidModuleEntryFile($this->moduleName),
            "{$this->moduleName}/public/style.css" => 'body { color: black; }',
        ];
        $this->createZipFile($this->storage->path($this->zipPath), $zipStructure);

        $uploader = $this->createModuleInstaller();

        // Extract and validate
        $method = new \ReflectionMethod(ModuleInstaller::class, 'extractToTemp');
        $method->setAccessible(true);
        $method->invoke($uploader);
        $method = new \ReflectionMethod(ModuleInstaller::class, 'validateSingleFolder');
        $method->setAccessible(true);
        $method->invoke($uploader);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('modules.upload_error_missing_entry');

        $method = new \ReflectionMethod(ModuleInstaller::class, 'checkEntryFile');
        $method->setAccessible(true);
        $method->invoke($uploader);
    }

    public function testCheckDisallowedPatternsThrowsExceptionOnFileUploadUsage()
    {
        // Create a ZIP with disallowed pattern
        $zipStructure = [
            "{$this->moduleName}/{$this->moduleName}.php" => '<?php $_FILES["test"];',
        ];
        $this->createZipFile($this->storage->path($this->zipPath), $zipStructure);

        $uploader = $this->createModuleInstaller();

        // Extract and validate
        $method = new \ReflectionMethod(ModuleInstaller::class, 'extractToTemp');
        $method->setAccessible(true);
        $method->invoke($uploader);
        $method = new \ReflectionMethod(ModuleInstaller::class, 'validateSingleFolder');
        $method->setAccessible(true);
        $method->invoke($uploader);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('modules.upload_error_file_upload_usage');

        $method = new \ReflectionMethod(ModuleInstaller::class, 'checkDisallowedPatterns');
        $method->setAccessible(true);
        $method->invoke($uploader);
    }

    public function testCheckFileExtensionsThrowsExceptionOnDisallowedExtension()
    {
        // Create a ZIP with disallowed extension
        $zipStructure = [
            "{$this->moduleName}/{$this->moduleName}.php" => $this->getValidModuleEntryFile($this->moduleName),
            "{$this->moduleName}/malicious.exe" => 'data',
        ];
        $this->createZipFile($this->storage->path($this->zipPath), $zipStructure);

        $uploader = $this->createModuleInstaller();

        // Extract and validate
        $method = new \ReflectionMethod(ModuleInstaller::class, 'extractToTemp');
        $method->setAccessible(true);
        $method->invoke($uploader);
        $method = new \ReflectionMethod(ModuleInstaller::class, 'validateSingleFolder');
        $method->setAccessible(true);
        $method->invoke($uploader);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('modules.upload_error_disallowed_filetype');

        $method = new \ReflectionMethod(ModuleInstaller::class, 'checkFileExtensions');
        $method->setAccessible(true);
        $method->invoke($uploader);
    }

    public function testCheckFileExtensionsThrowsExceptionOnDisallowedPublicExtension()
    {
        // Create a ZIP with disallowed public extension
        $zipStructure = [
            "{$this->moduleName}/{$this->moduleName}.php" => $this->getValidModuleEntryFile($this->moduleName),
            "{$this->moduleName}/public/script.php" => '<?php echo "test";',
        ];
        $this->createZipFile($this->storage->path($this->zipPath), $zipStructure);

        $uploader = $this->createModuleInstaller();

        // Extract and validate
        $method = new \ReflectionMethod(ModuleInstaller::class, 'extractToTemp');
        $method->setAccessible(true);
        $method->invoke($uploader);
        $method = new \ReflectionMethod(ModuleInstaller::class, 'validateSingleFolder');
        $method->setAccessible(true);
        $method->invoke($uploader);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('modules.upload_error_disallowed_public_filetype');

        $method = new \ReflectionMethod(ModuleInstaller::class, 'checkFileExtensions');
        $method->setAccessible(true);
        $method->invoke($uploader);
    }

    public function testCheckCoreOverrideProtectionThrowsExceptionOnCoreModuleOverride()
    {
        // Create a valid ZIP
        $zipStructure = [
            "{$this->moduleName}/{$this->moduleName}.php" => $this->getValidModuleEntryFile($this->moduleName, true),
        ];

        $this->createZipFile($this->storage->path($this->zipPath), $zipStructure);
        $uploader = $this->createModuleInstaller();
        $coreTestModule = $uploader->process();

        $this->assertTrue($coreTestModule->isCore());

        // Attempt to reextract the module
        // Create a valid ZIP
        $zipStructure = [
            "{$this->moduleName}/{$this->moduleName}.php" => $this->getValidModuleEntryFile($this->moduleName, false),
        ];

        $this->createZipFile($this->storage->path($this->zipPath), $zipStructure);
        $uploader = $this->createModuleInstaller();

        // Extract and validate
        $method = new \ReflectionMethod(ModuleInstaller::class, 'extractToTemp');
        $method->setAccessible(true);
        $method->invoke($uploader);
        $method = new \ReflectionMethod(ModuleInstaller::class, 'validateSingleFolder');
        $method->setAccessible(true);
        $method->invoke($uploader);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('modules.upload_error_override_core');

        $method = new \ReflectionMethod(ModuleInstaller::class, 'checkCoreOverrideProtection');
        $method->setAccessible(true);
        $method->invoke($uploader);
    }

    public function testMoveModuleSuccessfullyMovesFiles()
    {
        // Create a ZIP with public and non-public files
        $zipStructure = [
            "{$this->moduleName}/{$this->moduleName}.php" => $this->getValidModuleEntryFile($this->moduleName),
            "{$this->moduleName}/public/style.css" => 'body { color: black; }',
        ];
        $this->createZipFile($this->storage->path($this->zipPath), $zipStructure);

        $uploader = $this->createModuleInstaller();

        // Extract and validate
        $method = new \ReflectionMethod(ModuleInstaller::class, 'extractToTemp');
        $method->setAccessible(true);
        $method->invoke($uploader);
        $method = new \ReflectionMethod(ModuleInstaller::class, 'validateSingleFolder');
        $method->setAccessible(true);
        $method->invoke($uploader);

        $method = new \ReflectionMethod(ModuleInstaller::class, 'moveModule');
        $method->setAccessible(true);
        $method->invoke($uploader);

        $this->assertFileExists(
            $this->storage->path(
                $this->modulesDir . $this->moduleName . '/' . $this->moduleName . '.php'
            )
        );
        $this->assertFileExists(
            $this->storage->path(
                $this->publicDirName . '/' . basename($this->modulesDir) . '/' . $this->moduleName . '/style.css'
            )
        );
        $this->assertDirectoryDoesNotExist(
            $this->storage->path(
                $this->modulesDir . $this->moduleName . '/public'
            )
        );
    }

    public function testCleanUpDeletesTempDirectory()
    {
        // Create a ZIP
        $zipStructure = [
            "{$this->moduleName}/{$this->moduleName}.php" => $this->getValidModuleEntryFile($this->moduleName),
        ];
        $this->createZipFile($this->storage->path($this->zipPath), $zipStructure);

        $uploader = $this->createModuleInstaller();

        // Extract
        $method = new \ReflectionMethod(ModuleInstaller::class, 'extractToTemp');
        $method->setAccessible(true);
        $method->invoke($uploader);

        // Get tempDir
        $tempDirProp = new \ReflectionProperty(ModuleInstaller::class, 'tempDir');
        $tempDirProp->setAccessible(true);
        $tempDir = $tempDirProp->getValue($uploader);

        $this->assertDirectoryExists($this->storage->path($tempDir));

        $method = new \ReflectionMethod(ModuleInstaller::class, 'cleanUp');
        $method->setAccessible(true);
        $method->invoke($uploader);

        $this->assertDirectoryDoesNotExist($this->storage->path($tempDir));
    }
}
