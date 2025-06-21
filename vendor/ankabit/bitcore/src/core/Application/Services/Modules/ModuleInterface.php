<?php

namespace BitCore\Application\Services\Modules;

use BitCore\Foundation\Container;
use Exception;

/**
 * Interface ModuleInterface
 *
 * This interface defines the contract for a module in the application.
 */
interface ModuleInterface
{
    /**
     * Determine if languages for the module should be autoloaded
     *
     * @return Container
     */
    public function getContainer(): Container;

    /**
     * Get if the module is marked as core or not
     *
     * @return bool
     */
    public function isCore(): bool;

    /**
     * Get the unique ID of the module.
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Get the name of the module.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get the description of the module.
     *
     * @return string
     */
    public function getDescription(): string;

    /**
     * Get the version of the module.
     *
     * @return string
     */
    public function getVersion(): string;

    /**
     * Get the author name of the module.
     *
     * @return string
     */
    public function getAuthorName(): string;

    /**
     * Get the author URL of the module.
     *
     * @return string
     */
    public function getAuthorUrl(): string;

    /**
     * Get the base full base path for a the module.
     * @return string
     */
    public function getBasePath(): string;

    /**
     * Get the full path for a given module and subdirectory.
     * @param string $path Optional subdirectory path, relative to the module's base path.
     *  Defaults to an empty string.
     * @return string
     */
    public function getPath($path = ''): string;

    /**
     * Get the module namespace name
     *
     * @return string
     */
    public function getNamespace(): string;

    /**
     * Order to load the module. Higher value get loaded earlier
     *
     * @return integer
     */
    public function getPriority(): int;

    /**
     * Get the module local metadata from metadata.json file
     *
     * @return array
     */
    public function getMetadata(): array;

    /**
     * Load the language files for the module.
     *
     * @return void
     */
    public function loadLanguage(): void;

    /**
     * Load the route files for the module.
     * The route file should be placed in 'Config' folder i.e Config/routes.php
     * and should return callable
     *
     * @return void
     */
    public function loadRoutes(): void;

    /**
     * Register the module's services into the application's dependency injection container.
     * This method handles binding of providers, repositories, and other services to the container.
     * Additionally, event listeners are registered here.
     *
     * @return void
     */
    public function register(): void;

    /**
     * Boot the module.
     * This method initializes the module, performing any necessary setup such as
     * loading language files, checking module state, or executing other active logic.
     *
     * @return void
     */
    public function boot(): void;

    /**
     * Method to abstract some activities before calling the register method on the module
     *
     * @return void
     */
    public function beforeRegister(): void;

    /**
     * Method to abstract some activities before calling the boot method on the module
     *
     * @return void
     */
    public function beforeBoot(): void;

    /**
     * Install the module.
     *
     * @throws Exception If an error occurs during installation.
     */
    public function install(): void;

    /**
     * Activate the module.
     *
     * @throws Exception If an error occurs during activation.
     */
    public function activate(): void;

    /**
     * Deactivate the module.
     *
     * @throws Exception If an error occurs during deactivation.
     */
    public function deactivate(): void;

    /**
     * Uninstall the module.
     *
     * @throws Exception If an error occurs during uninstallation.
     */
    public function uninstall(): void;
}
