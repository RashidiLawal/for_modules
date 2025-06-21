<?php

declare(strict_types=1);

use BitCore\Modules\Settings\Repositories\SettingsRepository;

/**
 * Get Settings
 *
 * @return SettingsRepository
 */
function settings(): SettingsRepository
{
    return container(SettingsRepository::class);
}
