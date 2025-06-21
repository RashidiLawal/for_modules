<?php

declare(strict_types=1);

namespace BitCore\Application\Providers;

use BitCore\Kernel\App;

/**
 * Abstract base class for all service providers in the application.
 *
 * Service providers are responsible for registering and bootstrapping services
 * and components within the application's service container. They allow for the
 * dynamic configuration and dependency management of services used throughout
 * the application.
 */
abstract class ProviderAbstract
{
    /**
     * @var App The application instance.
     *
     * The `$app` property holds the reference to the application container,
     * which is responsible for managing and resolving dependencies.
     */
    protected $app;

    /**
     * Constructor.
     *
     * Initializes the service provider with the application instance.
     * The `$app` instance is typically used to bind services to the container
     * or resolve services that the provider might need.
     *
     * @param App $app The application instance.
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * Register services and components within the application container.
     *
     * This method is used to bind services or components to the container
     * so that they can be resolved and injected into other parts of the application.
     * This method should be implemented by concrete service provider classes.
     * It is typically called early in the application's lifecycle.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Boot any services that need to be initialized or executed after registration.
     *
     * This method is intended to execute any setup or initialization logic
     * for services that have already been registered in the container.
     * It is generally used to perform tasks like event listener registration
     * or executing initial tasks that need to be done after all services have
     * been registered.
     *
     * @return void
     */
    public function boot()
    {
    }
}
