<?php

declare(strict_types=1);

namespace BitCore\Kernel;

use BitCore\Application\Events\AppLoadedEvent;
use BitCore\Application\Events\AppProvidersRegisteredEvent;
use BitCore\Application\Services\Settings\SystemConfig;
use BitCore\Foundation\Container;
use BitCore\Kernel\App;
use BitCore\Kernel\Factory\AppFactory;
use BitCore\Kernel\Handlers\HttpErrorHandler;
use BitCore\Kernel\Handlers\ShutdownHandler;
use BitCore\Kernel\ResponseEmitter\ResponseEmitter;
use Dotenv\Dotenv;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

/**
 * Bootstrap class responsible for initializing and running the application.
 */
class Bootstrap
{
    /**
     * @var App Application instance.
     */
    private $app;

    /**
     * @var Container Dependency injection container.
     */
    private $container;

    /**
     * Bootstrap constructor.
     *
     * Loads environment variables and initializes the application.
     */
    public function __construct()
    {
        $this->autoload();

        // Init the session if not yet done
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->loadEnv();

        $this->initialize();
    }

    private function autoload()
    {
        require_once __DIR__ . '/../Application/Config/autoload.php';
    }

    /**
     * Loads environment variables from `.env` files.
     */
    private function loadEnv(): void
    {
        $envFiles = [];
        $basePath = base_path();

        if (file_exists($basePath . '.env')) {
            $envFiles[] = '.env';
        }

        // Optinally add for test to make testing easy
        if (file_exists($basePath . '.env.test')) {
            array_unshift($envFiles, '.env.test');
        }

        $dotenv = Dotenv::createImmutable(
            $basePath,
            $envFiles,
        );
        $dotenv->load();

        // Require some important variables loaded
        $dotenv->required([
            'DB_DATABASE',
        ]);
    }

    /**
     * Initializes the application container, settings, and dependencies.
     */
    private function initialize(): void
    {
        // Create and configure the container
        $this->container = new Container();

        AppFactory::setContainer($this->container);
        $this->app = App::setInstance(AppFactory::create());
        $this->app->setContainer($this->container);

        // Load service providers
        $this->loadProviders('register'); // Register providers

        // Dispatch an event to notify about app creation
        hooks()->dispatch(AppProvidersRegisteredEvent::class, new AppProvidersRegisteredEvent($this->app));

        $this->loadProviders('boot'); // Boot providers

        // Dispatch an event to notify about app readiness
        hooks()->dispatch(AppLoadedEvent::class, new AppLoadedEvent($this->app));
    }

    /**
     * Loads service providers registered/boot methods in `providers.php`.
     * @param string  $method The method on the provider to summon.
     */
    private function loadProviders(string $method): void
    {
        $providers = read_config_array('providers.php');
        foreach ($providers as $providerClass) {
            $instance = new $providerClass($this->app);
            if (method_exists($instance, $method)) {
                $instance->$method();
            }
        }
    }

    /**
     * Sets up error handling middleware.
     *
     * @param ServerRequestInterface $request The incoming request.
     */
    private function setErrorHandler(ServerRequestInterface $request): void
    {
        /** @var SystemConfig $settings */
        $settings = $this->container->get(SystemConfig::class);

        // Create Error Handler
        $callableResolver = $this->app->getCallableResolver();
        $responseFactory = $this->app->getResponseFactory();
        $errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);

        // Create Shutdown Handler
        $shutdownHandler = new ShutdownHandler(
            $request,
            $errorHandler,
            $settings->get('displayErrorDetails'),
            $settings->get('logError'),
            $settings->get('logErrorDetails')
        );
        register_shutdown_function($shutdownHandler);

        // Add Middleware
        $this->app->addRoutingMiddleware();
        $this->app->addBodyParsingMiddleware();

        $errorMiddleware = $this->app->addErrorMiddleware(
            $settings->get('displayErrorDetails'),
            $settings->get('logError'),
            $settings->get('logErrorDetails'),
            $this->container->get(LoggerInterface::class)
        );
        $errorMiddleware->setDefaultErrorHandler($errorHandler);
    }

    /**
     * Runs the application and emits the response.
     */
    public function run(): void
    {

        $this->setErrorHandler($this->app->getRequest());

        // Handle request and emit response
        $response = $this->app->handle($this->app->getRequest());
        (new ResponseEmitter())->emit($response);
    }

    /**
     * Runs the application in test mode and returns the application instance.
     *
     * @return App The application instance.
     */
    public function runTest(): App
    {
        // Create Request object from globals
        $this->setErrorHandler($this->app->getRequest());

        return $this->app;
    }
}
