<?php

use BitCore\Foundation\Container;
use BitCore\Modules\Settings\Repositories\SettingsRepository;

return function (Container $container) {
    // Update settings instance and load from storage
    $container->bind(SettingsRepository::class, function () {
        $settings = new SettingsRepository([]);
        return $settings->load();
    });
};
