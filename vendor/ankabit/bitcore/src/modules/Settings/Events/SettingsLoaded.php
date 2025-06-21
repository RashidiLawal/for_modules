<?php

namespace BitCore\Modules\Settings\Events;

use BitCore\Modules\Settings\Repositories\SettingsRepository;

class SettingsLoaded
{
    public SettingsRepository $settings;

    public function __construct(SettingsRepository $settings)
    {
        $this->settings = $settings;
    }
}
