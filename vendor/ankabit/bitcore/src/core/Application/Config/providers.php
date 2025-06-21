<?php

/**
 * Provider registery for the app.
 */

use BitCore\Application\Providers\AppServiceProvider;
use BitCore\Application\Providers\EventsProvider;
use BitCore\Application\Providers\MiddlewaresProvider;
use BitCore\Application\Providers\ModulesProvider;
use BitCore\Application\Providers\RoutesProvider;
use BitCore\Application\Providers\SystemConfigProvider;
use BitCore\Application\Providers\TestingServiceProvider;

return [
    // Prioritize events provider as it used by most other provider
    EventsProvider::class,
    SystemConfigProvider::class,

    // Load common resources like DB, Queue, Translation e.t.c
    AppServiceProvider::class,

    // Load common test resources
    TestingServiceProvider::class,

    // Load modules
    ModulesProvider::class,

    MiddlewaresProvider::class,
    RoutesProvider::class,
];
