<?php

declare(strict_types=1);

namespace BitCore\Application\Providers;

use BitCore\Application\Middleware\CsrfMiddleware;
use BitCore\Application\Middleware\SessionMiddleware;
use BitCore\Application\Providers\ProviderAbstract;

/**
 * Provide general common middlewar for the app.
 */
class MiddlewaresProvider extends ProviderAbstract
{
    /**
     * {@inheritDoc}
     */
    public function register()
    {
        // Register csrf middleware
        $this->app->getContainer()->singleton('csrf', function () {

            // @todo Move most of these settings to .env
            $prefix = 'csrf';
            $storage = null;
            $failureHandler = null;
            $storageLimit = 200;
            $strength = 16;
            $persistentTokenMode = true;

            return new CsrfMiddleware(
                $this->app->getResponseFactory(),
                $prefix,
                $storage,
                $failureHandler,
                $storageLimit,
                $strength,
                $persistentTokenMode
            );
        });
    }

    public function boot()
    {
        // Register the session middleware
        $this->app->add(SessionMiddleware::class);

        // Dispatch the event to allow other parts of the application to modify the excluded paths
        $csrfSettings = (array)config()->get('csrf');
        if ($csrfSettings['enabled'] !== false) { //@todo Test value switch and stictness
            $this->app->add('csrf');
        }
    }
}
