<?php

declare(strict_types=1);

namespace BitCore\Application\Providers;

use BitCore\Application\Providers\ProviderAbstract;
use BitCore\Application\Services\Settings\SystemConfig;

/**
 * Provide the setting for the whole app
 */
class SystemConfigProvider extends ProviderAbstract
{
    /**
     * {@inheritDoc}
     */
    public function register()
    {
        // Register global system configuration
        $this->app->getContainer()->singleton(
            SystemConfig::class,
            function () {
                $config = read_config_array('common.php');
                return new SystemConfig($config);
            }
        );
    }
}
