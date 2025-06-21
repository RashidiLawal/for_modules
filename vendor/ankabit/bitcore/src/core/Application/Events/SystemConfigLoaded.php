<?php

namespace BitCore\Application\Events;

use BitCore\Application\Services\Settings\SystemConfig;

class SystemConfigLoaded
{
    public SystemConfig $settings;

    public function __construct(SystemConfig $settings)
    {
        $this->settings = $settings;
    }
}
