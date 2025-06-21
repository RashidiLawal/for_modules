<?php

namespace BitCore\Application\Events;

use BitCore\Kernel\App;

/**
 * Event triggered when the application instance is fully loaded and ready.
 *
 * This event can be used to perform actions or register listeners
 * after the application instance is ready and provider and modules are loaded.
 * It idicate the core app and modules are loaded.
 */
final class AppLoadedEvent
{
    /**
     * @var App The application instance.
     */
    private App $app;

    /**
     * Constructs a new AppCreatedEvent instance.
     *
     * @param App $app The application instance.
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * Gets the application instance.
     *
     * @return App The application instance.
     */
    public function getApp(): App
    {
        return $this->app;
    }
}
