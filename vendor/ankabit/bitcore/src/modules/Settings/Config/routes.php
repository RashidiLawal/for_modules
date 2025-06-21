<?php

declare(strict_types=1);

use BitCore\Kernel\App;
use BitCore\Modules\Settings\Actions\ListSettingsAction;
use BitCore\Modules\Settings\Actions\SaveSettingsAction;

return function (App $app) {
    $app->group('/api/settings', function ($group) {
        // List all settings or by group/id
        $group->get('', ListSettingsAction::class)
            ->setName('settings.index');

        $group->get('/{group}', ListSettingsAction::class)
            ->setName('settings.group');

        $group->get('/{group}/{id}', ListSettingsAction::class)
            ->setName('settings.groupItem');

        // Save/update settings
        $group->post('', SaveSettingsAction::class)
            ->setName('settings.store');
    });
};
