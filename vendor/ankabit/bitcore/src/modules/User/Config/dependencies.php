<?php

use BitCore\Application\Events\AppLoadedEvent;
use BitCore\Modules\User\Models\User;

// Listen to app created event and load the routes
hooks()->listen(AppLoadedEvent::class, function (AppLoadedEvent $event) {

    /** @var \BitCore\Kernel\App $app  */
    $app = $event->getApp();

    // Load some middleware
    //$app->add($yourMiddlewareInstance);

    // Load routes if not autoloading
    $routes = require __DIR__ . '/routes.php';
    $routes($app);
});

hooks()->listen('user.created', function (User $user) {
    // Handle user created event
    echo "User created: " . $user->email;
});

User::created(function (User $user) {
    hooks()->dispatch('user.created', $user);
});
