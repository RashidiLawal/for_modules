<?php

declare(strict_types=1);

namespace BitCore\Modules\ModulesManager\Services;

use BitCore\Application\Services\Modules\ModuleInterface;
use BitCore\Application\Services\Modules\ModuleRegistry;
use BitCore\Foundation\Filesystem\FilesystemInterface;
use ZipArchive;
use Exception;

/**
 * Class ModuleUploader
 *
 * Handles the uploading, extraction, validation, and installation of modules.
 */
class ModuleUploader
{
    /**
     * Path to the uploaded zip file on local storage.
     *
     * @var string
     */
    protected string $zipPath;

    /**
     * Stroage: should ideally have 'root' as the project base path i.e base_path()
     *
     * @var FilesystemInterface
     */
    protected $storage;

    /**
     * ModuleRegistry
     * @var ModuleRegistry
     */
    protected $moduleRegistry;

    /**
     * Temporary directory for extraction.
     *
     * @var string
     */
    protected string $tempDir;

    /**
     * Extracted module directory path.
     *
     * @var string
     */
    protected string $moduleDir;

    /**
     * Final destination where extracted module will be moved to
     *
     * @var string
     */
    protected string $modulesDir;

    /**
     * Module folder name.
     *
     * @var string
     */
    protected string $moduleName;

    /**
     * Allowed public file extensions inside module package.
     *
     * @var array
     */
    protected array $allowedPublicExtensions = [
        'html', 'htm', 'css', 'js', 'json',
        'xml', 'yml', 'yaml', 'htaccess',
        'jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'ico',
        'ttf', 'otf', 'woff', 'woff2',
        'md', 'txt', 'pdf', 'csv', 'zip', 'map',
        'xlsx', 'xls', 'docx', 'doc', 'pdf'
    ];

    /**
     * Extensions not allowed inside public/ folder
     *
     * @var array
     */
    protected array $nonPublicExtensions = [
        'php', 'sql', 'sqlite', 'dist', 'lock', '.env', '.test'
    ];

    /**
     * The directory name used to detect public files or assets for publishing.
     *
     * @var string
     */
    protected string $publicDirName;

    /**
     * The directory name used to detect Composer-managed dependencies.
     *
     * @var string
     */
    protected string $vendorDirName;

    /**
     * Temporary directory base folder name.
     * The tempDir will be created inside this space.
     */
    protected string $tempDirName;

    /**
     * ModuleUploader constructor.
     *
     * Initializes a new instance of the ModuleUploader service, responsible for handling
     * the extraction, validation, and installation of module packages into the application.
     *
     * @param string               $zipPath         The zip file path relative to the local storage disk.
     * @param FilesystemInterface  $storage         The storage filesystem instance.
     * @param string               $modulesDir      The base directory where extracted modules will be stored
     *  (with trailing slash).
     * @param ModuleRegistry       $moduleRegistry  The module registry instance for loading and registering modules.
     * @param string               $publicDirName   (Optional) Directory name for public assets.
     * Defaults to 'public'.
     * @param string               $vendorDirName   (Optional) Directory name for vendor dependencies.
     * Defaults to 'vendor'.
     * @param string               $tempDirName   (Optional) Directory name to create
     * unique temporary folder for extraction.
     */
    public function __construct(
        string $zipPath,
        $storage,
        string $modulesDir,
        ModuleRegistry $moduleRegistry,
        string $publicDirName = 'public',
        string $vendorDirName = 'vendor',
        string $tempDirName = 'temp',
    ) {
        $this->zipPath        = $zipPath;
        $this->storage        = $storage;
        $this->modulesDir     = $modulesDir;
        $this->moduleRegistry = $moduleRegistry;
        $this->publicDirName  = $publicDirName;
        $this->vendorDirName  = $vendorDirName;
        $this->tempDirName = $tempDirName;
    }


    /**
     * Process the uploaded module zip file: extract, validate and install.
     *
     * @return ModuleInterface|null
     *
     * @throws Exception
     */
    public function process(): ?ModuleInterface
    {
        $this->extractToTemp();
        $this->validateSingleFolder();
        $this->checkEntryFile();
        $this->checkDisallowedPatterns();
        $this->checkFileExtensions();
        $this->checkCoreOverrideProtection();
        $this->moveModule();
        $this->cleanUp();

        // Load the module and return the module instance
        $this->moduleRegistry->loadModules([$this->moduleName]);
        return $this->moduleRegistry->findModuleById($this->moduleName);
    }

    /**
     * Extract the zip file into a unique temporary directory.
     *
     * @throws Exception
     */
    protected function extractToTemp(): void
    {
        $this->tempDir = $this->tempDirName . '/module_' . bin2hex(random_bytes(16));
        $this->storage->makeDirectory($this->tempDir);

        $zip = new ZipArchive();

        $localZipPath = $this->storage->path($this->zipPath);

        if ($zip->open($localZipPath) !== true) {
            throw new Exception(trans('modules.upload_error_open_zip'));
        }

        $extractionPath = $this->storage->path($this->tempDir);
        $zip->extractTo($extractionPath);
        $zip->close();
    }

    /**
     * Validate that the extracted archive contains only one folder.
     *
     * @throws Exception
     */
    protected function validateSingleFolder(): void
    {
        $extractionPath = $this->storage->path($this->tempDir);
        $contents = collect(scandir($extractionPath))
            ->reject(fn ($item) => in_array($item, ['.', '..']))
            ->values();

        if ($contents->count() !== 1 || !is_dir($extractionPath . '/' . $contents[0])) {
            throw new Exception(trans('modules.upload_error_multiple_folders'));
        }

        $this->moduleName = $contents[0];
        $this->moduleDir  = $this->tempDir . '/' . $this->moduleName;
    }

    /**
     * Check module contains the required entry file and not both.
     *
     * @throws Exception
     */
    protected function checkEntryFile(): void
    {
        $phpPath = $this->moduleDir . '/' . $this->moduleName . '.php';
        $phpExists = $this->storage->exists($phpPath);
        if (!$phpExists) {
            throw new Exception(trans('modules.upload_error_missing_entry'));
        }

        // Conditionally check for public entry file
        $jsPath  = $this->moduleDir . '/' . $this->publicDirName;
        if ($this->storage->exists($jsPath)) { // If module has public folder, enforce entry file for it.
            $jsPath .= '/' . $this->moduleName . '.js';
            $jsExists  = $this->storage->exists($jsPath);
            if (!$jsExists) {
                throw new Exception(trans('modules.upload_error_missing_entry'));
            }
        }
    }

    /**
     * Validate files for disallowed patterns in code.
     *
     * @throws Exception
     */
    protected function checkDisallowedPatterns(): void
    {
        $files = $this->storage->allFiles($this->moduleDir);

        foreach ($files as $file) {
            $content = $this->storage->get($file);

            if (preg_match('/\$_FILES/', $content)) {
                throw new Exception(trans('modules.upload_error_file_upload_usage'));
            }

            if (preg_match('/namespace\s+(Illuminate|Slim)\\\\/', $content)) {
                throw new Exception(trans('modules.upload_error_disallowed_namespace'));
            }

            if (preg_match('/\$this->request->getParsedBody\s*\(/', $content)) {
                throw new Exception(trans('modules.upload_error_parsed_body'));
            }

            if (preg_match('/DB::select\s*\(|DB::statement\s*\(/', $content)) {
                throw new Exception(trans('modules.upload_error_raw_queries'));
            }
        }
    }

    /**
     * Ensure only allowed file extensions exist in the module,
     * and no disallowed types are placed inside public directory.
     * Vendor or composer publish folder will be skipped
     *
     * @throws Exception
     */
    protected function checkFileExtensions(): void
    {
        $allowedExtensions = array_merge($this->allowedPublicExtensions, $this->nonPublicExtensions);

        $files = $this->storage->allFiles($this->moduleDir);

        foreach ($files as $file) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

            // Skip checks in vendor directory
            if (str_starts_with($file, $this->moduleDir . '/' . $this->vendorDirName)) {
                continue;
            }

            // Check allowed extensions globally
            if (!in_array($ext, $allowedExtensions)) {
                throw new Exception(trans(
                    'modules.upload_error_disallowed_filetype',
                    ['filename' => basename($file)]
                ));
            }

            // Check non-public extensions inside public/
            if (
                str_starts_with($file, $this->moduleDir . '/' . $this->publicDirName) &&
                in_array($ext, $this->nonPublicExtensions)
            ) {
                throw new Exception(trans(
                    'modules.upload_error_disallowed_public_filetype',
                    ['filename' => basename($file)]
                ));
            }
        }
    }


    /**
     * Prevent overriding core modules during upload.
     *
     * @throws Exception
     */
    protected function checkCoreOverrideProtection(): void
    {
        foreach ($this->moduleRegistry->getModules() as $module) {
            if ($module->isCore() && $module->getId() == $this->moduleName) {
                throw new Exception(trans('modules.upload_error_override_core', ['module' => $this->moduleName]));
            }
        }
    }

    /**
     * Move validated module to final modules directory.
     *
     * @throws Exception
     */
    protected function moveModule(): void
    {
        $source = $this->moduleDir;
        $target = $this->modulesDir . $this->moduleName;

        $publicFolder = $this->publicDirName;

        $moveDir = function ($from, $to) {
            $files = $this->storage->allFiles($from);
            foreach ($files as $file) {
                $newPath = str_replace($from, $to, $file);

                // Move file within storage
                $this->storage->move($file, $newPath);
            }
        };

        // Move all module files
        $moveDir($source, $target);


        // Publish public content if exists
        $publicSource = $target . '/' . $publicFolder;
        $publicTarget = $publicFolder . '/' . basename($this->modulesDir) . '/' . $this->moduleName;

        if ($this->storage->exists($publicSource)) {
            $moveDir($publicSource, $publicTarget);

            // Remove the public dir
            $this->storage->deleteDirectory($publicSource);
        }
    }

    /**
     * Remove temporary extraction directory.
     */
    protected function cleanUp(): void
    {
        $this->storage->deleteDirectory($this->tempDir);
    }
}
