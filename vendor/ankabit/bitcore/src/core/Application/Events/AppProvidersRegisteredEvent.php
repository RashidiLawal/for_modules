<?php

namespace BitCore\Application\Events;

use BitCore\Kernel\App;

/**
 * Event triggered when the application providers are registered but not yet loaded.
 */
final class AppProvidersRegisteredEvent
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
