<?php

use BitCore\Foundation\Container;
use BitCore\Modules\Settings\Repositories\SettingsRepository;

return function (Container $container) {
    // Register with empty instance of the settings
    $container->bind(SettingsRepository::class, fn () => new SettingsRepository([]));
};
