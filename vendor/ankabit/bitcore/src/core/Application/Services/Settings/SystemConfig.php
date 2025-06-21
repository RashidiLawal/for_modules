<?php

declare(strict_types=1);

namespace BitCore\Application\Services\Settings;

use BitCore\Application\Events\SystemConfigLoaded;

/**
 *
 * SystemConfig class for file baseds configurations.
 *
 */
class SystemConfig implements SettingsInterface
{
    protected array $settings;

    protected string $eventClass = SystemConfigLoaded::class;

    public function __construct(array $settings = [])
    {
        $this->setSettings($settings);
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $key, $default = null)
    {
        return $this->settings[$key] ?? $default;
    }

    /**
     * {@inheritDoc}
     */
    public function getAsBool(string $key): bool
    {
        $value = $this->get($key);

        if (is_bool($value)) {
            return $value;
        }

        // Check for falsy values (null, '', 0, false, etc.)
        if (empty($value)) {
            return false; // Return false if the value is empty or falsy.
        }

        // Define truthy values to return true
        $truthyValues = ['1', 'yes', 'true'];

        // Normalize value and check if it's one of the truthy values
        if (in_array(strtolower($value), $truthyValues, true)) {
            return true;
        }

        // Return false for any other non-truthy values
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getAll(): array
    {
        return $this->settings;
    }

    /**
     * {@inheritDoc}
     */
    public function setSettings(array $settings): void
    {
        $this->settings = $settings;

        // Dispatch the event dynamically
        if ($this->eventClass) {
            hooks()->dispatch($this->eventClass, $this);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function set(string $key, mixed $value): void
    {
        $this->setMany(["$key" => $value]);
    }

    /**
     * {@inheritDoc}
     */
    public function setMany(array $data): void
    {
        foreach ($data as $key => $value) {
            $this->settings[$key] = $value;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function save(string $key, mixed $value): bool
    {
        return $this->saveMany(["$key" => $value]) == 1;
    }


    /**
     * {@inheritDoc}
     */
    public function saveMany(array $data): int
    {
        return count($data);
    }
}
