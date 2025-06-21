<?php

declare(strict_types=1);

namespace BitCore\Application\Providers;

use BitCore\Application\Providers\ProviderAbstract;
use BitCore\Foundation\Events\Dispatcher;

/**
 * Provide the event dispatcher.
 * This should be loaded very early to all early access to hooks/events
 */
class EventsProvider extends ProviderAbstract
{
    /**
     * {@inheritDoc}
     */
    public function register()
    {
        // Register Events Dispatcher early here to ensure its availability to others
        $this->app->getContainer()->singleton(Dispatcher::class, function ($container) {
            return new Dispatcher($container);
        });
    }
}
