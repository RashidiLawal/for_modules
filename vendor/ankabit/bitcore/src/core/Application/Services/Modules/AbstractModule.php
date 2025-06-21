<?php

namespace BitCore\Application\Services\Modules;

use BitCore\Foundation\Container;
use BitCore\Application\Services\Modules\MigrationTrait;
use BitCore\Application\Services\Settings\SettingsInterface;
use BitCore\Foundation\Translation\Translator;

/**
 * Abstract Class AbstractModule
 *
 * This abstract class provides default implementations for the ModuleInterface.
 */
abstract class AbstractModule implements ModuleInterface
{
    use MigrationTrait;

    /**
     * The application container instance.
     *
     * @var Container
     */
    protected $container;

    /**
     * Indicates whether this is a core module.
     *
     * @var bool
     */
    protected $isCore = false;

    /**
     * The unique id of the module.
     *
     * @var string
     */
    protected $id;

    /**
     * The name of the module.
     *
     * @var string
     */
    protected $name;

    /**
     * The description of the module.
     *
     * @var string
     */
    protected $description;

    /**
     * The version of the module.
     *
     * @var string
     */
    protected $version;

    /**
     * The author name of the module.
     *
     * @var string
     */
    protected $authorName;

    /**
     * The author URL of the module.
     *
     * @var string
     */
    protected $authorUrl;

    /**
     * The priority for loading order. High value get loaded first
     *
     * @var integer
     */
    protected $priority = 1;

    /**
     * This property indicates whether the autoload route is enabled.
     *
     * @var boolean
     */
    protected $autoloadRoute = true;

    /**
     * This property indicates whether to autoload config register for dependency injection.
     *
     * @var boolean
     */
    protected $autoloadRegister = false;

    /**
     * This property indicates whether to autoload config boot file.
     *
     * @var boolean
     */
    protected $autoloadBoot = false;

    /**
     * The module settings interface
     *
     * @var SettingsInterface
     */
    public $settings;

    /**
     * The base path where the module is contained
     */
    protected $basePath;

    /**
     * The base namespace for the module loading
     */
    protected $baseNamespace;


    /**
     * AbstractModule constructor.
     *
     * @param Container $container
     * @param string $basePath
     * @param string $baseNamespace
     */
    public function __construct(Container $container, $basePath, $baseNamespace)
    {
        $this->container = $container;
        $this->basePath = $basePath;
        $this->baseNamespace = $baseNamespace;
        $this->checkRequiredAttributes();
    }

    /**
     * Determine if languages for the module should be autoloaded
     *
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * Check if required attributes are set.
     *
     * @return void
     * @throws \Exception if a required attribute is missing.
     */
    protected function checkRequiredAttributes(): void
    {
        if (empty($this->id)) {
            throw new \Exception("The 'id' attribute is required.");
        }

        if (empty($this->version)) {
            throw new \Exception("The 'version' attribute is required.");
        }
    }

    /**
     * Get the value of the core status.
     *
     * @return bool
     */
    public function isCore(): bool
    {
        return $this->isCore;
    }

    /**
     * Get the unique id of the module.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get the name of the module.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the description of the module.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Get the version of the module.
     *
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Get the author name of the module.
     *
     * @return string
     */
    public function getAuthorName(): string
    {
        return $this->authorName;
    }

    /**
     * Get the author URL of the module.
     *
     * @return string
     */
    public function getAuthorUrl(): string
    {
        return $this->authorUrl;
    }

    /**
     * Get the module loading priorty. Lower value get loaded first
     *
     * @return integer
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * {@inheritDoc}
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * Get the full path for a given module and subdirectory.
     *
     * This method constructs a path by appending the module's ID to the base
     * path, followed by the optional subdirectory path provided.
     *
     * @param string $path Optional subdirectory path, relative to the module's base path.
     *                     Defaults to an empty string.
     * @return string The full resolved path for the module.
     */
    public function getPath($path = ''): string
    {
        return $this->basePath . $this->getId() . DIRECTORY_SEPARATOR . (ltrim($path, DIRECTORY_SEPARATOR));
    }

    /**
     * Get the full base namespace for the module
     *
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->baseNamespace . $this->getId();
    }

    /**
     * {@inheritDoc}
     */
    public function getMetadata(): array
    {
        $metaPath = $this->getPath('/metadata.json');

        if (!file_exists($metaPath)) {
            return [];
        }

        $metadata = json_decode(file_get_contents($metaPath), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception(trans('modules.upload_error_invalid_json'));
        }

        return $metadata;
    }

    /**
     * Load the language files for the module.
     *
     * This method checks if the `lang` directory exists in the module's path.
     * If the directory exists, it registers the path with the Translator service
     * under a namespace matching the module's ID. This allows the module's
     * language files to be used via translation keys prefixed by the module's ID.
     *
     * @return void
     */
    public function loadLanguage(): void
    {
        $langPath = $this->getPath('lang'); // Resolve the language directory path.

        if (is_dir($langPath)) {
            $this->container
                ->get(Translator::class) // Retrieve the Translator instance from the container.
                ->getLoader()
                ->addNamespace($this->id, $langPath); // Register the language path under the module's namespace.
        }
    }

    /**
     * {@inheritDoc}
     */
    public function loadRoutes(): void
    {
        $routeFile = $this->getPath('Config/routes.php');

        if (is_file($routeFile)) {
            $routes = require $routeFile;
            $routes(app());
        }
    }

    /**
     * {@inheritDoc}
     */
    public function register(): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function boot(): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function beforeRegister(): void
    {
        if ($this->autoloadRegister) {
            $register = require($this->getPath('Config/register.php'));
            $register($this->container);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function beforeBoot(): void
    {
        // Inject the settings
        $this->settings = settings()->useGroup('module', $this->id);

        // Attempt to load the module languages.
        $this->loadLanguage();

        // Attempt to autoload routes
        if ($this->autoloadRoute) {
            $this->loadRoutes();
        }

        if ($this->autoloadBoot) {
            $boot = require($this->getPath('Config/boot.php'));
            $boot($this->container);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function install(): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function activate(): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function deactivate(): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function uninstall(): void
    {
    }
}
