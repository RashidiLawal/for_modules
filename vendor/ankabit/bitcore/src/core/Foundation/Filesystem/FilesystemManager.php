<?php

declare(strict_types=1);

namespace BitCore\Foundation\Filesystem;

use Illuminate\Filesystem\FilesystemManager as IlluminateFilesystemManager;

class FilesystemManager extends IlluminateFilesystemManager
{
    /**
     * Get the filesystem connection configuration.
     *
     * @param  string  $name
     * @return array
     */
    protected function getConfig($name)
    {
        return $this->app['config']["disks"]["{$name}"] ?: [];
    }

    /**
     * Set the filesystem connection configuration
     *
     * @param array $config
     * @return void
     */
    public function setConfig(array $config)
    {
        $this->app['config'] = $config;
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']['default'];
    }

    /**
     * Get the default cloud driver name.
     *
     * @return string
     */
    public function getDefaultCloudDriver()
    {
        return $this->app['config']['cloud'] ?? 's3';
    }
}
