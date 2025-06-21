<?php

declare(strict_types=1);

namespace BitCore\Kernel;

use BitCore\Foundation\Container;
use Psr\Http\Message\ResponseInterface;
use Slim\App as SlimApp;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\ServerRequestCreatorFactory;

/**
 * Extends the Slim\App class to provide custom functionality
 * such as dependency injection and singleton pattern.
 */
class App extends SlimApp
{
    /**
     * @var self|null The singleton instance of the App class.
     */
    private static ?self $instance = null;

    /**
     * @var Container The dependency injection container.
     */
    private ?Container $appContainer = null;

    /**
     * @var ServerRequestInterface The http request.
     */
    private ?ServerRequestInterface $request = null;

    /**
     * Get the current app instance (singleton pattern).
     *
     * @return self|null The current app instance, or null if not set.
     */
    public static function getInstance(): ?self
    {
        return self::$instance;
    }

    /**
     * Set the current app instance (singleton pattern).
     *
     * @param self $instance The app instance to set.
     * @return self The set app instance.
     */
    public static function setInstance($instance): ?self
    {
        self::$instance = $instance;
        return $instance;
    }

    /**
     * Set the dependency injection container for the application.
     *
     * @param Container $container The dependency injection container.
     */
    public function setContainer(Container $container)
    {
        $this->appContainer = $container;
    }

    /**
     * Get the dependency injection container for the application.
     *
     * @return Container|null The dependency injection container, or null if not set.
     */
    public function getContainer(): ?Container
    {
        return $this->appContainer;
    }

    /**
     * Return the Http PSR7 Request
     *
     * @return ServerRequestInterface
     */
    public function getRequest(): ServerRequestInterface
    {

        if ($this->request) {
            return $this->request;
        }

        // Create Request object from globals
        $serverRequestCreator = ServerRequestCreatorFactory::create();
        $this->request = $serverRequestCreator->createServerRequestFromGlobals();

        return $this->request;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->request = $request;
        return parent::handle($request);
    }
}
