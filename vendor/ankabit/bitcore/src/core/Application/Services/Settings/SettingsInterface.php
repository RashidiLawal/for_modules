<?php

declare(strict_types=1);

namespace BitCore\Application\Services\Settings;

/**
 * SettingsInterface defines the contract for managing configuration settings.
 */
interface SettingsInterface
{
    public function __construct(array $settings = []);

    /**
     * Get a configuration value by key.
     *
     * @param string $key. The configuration key to retrieve.
     * @param string $default. The default value to return if the $key is not set.
     * @return string|array|object|bool The configuration value.
     */
    public function get(string $key, $default = null);

    /**
     * Reset the settings with a new configuration array.
     *
     * @param array $settings The new configuration settings.
     * @return void
     */
    public function setSettings(array $settings): void;

    /**
     * Retrieve all configuration settings as an associative array.
     *
     * @return array The configuration settings.
     */
    public function getAll(): array;


    /**
     * Resolves the value of a given settings key to a boolean (true or false).
     *
     * This method retrieves the value associated with the provided key and returns
     * a boolean value based on its content. It considers specific truthy values
     * such as 'yes', '1', and 'true' to return `true`. Any other non-falsy values
     * will return `false`, with no fallback option for falsy values.
     *
     * @param string $key The key to retrieve the value from.
     * @return bool The boolean representation of the retrieved value.
     */
    public function getAsBool(string $key): bool;

    /**
     * Set a configuration key with a value.
     * This method does not persist the value to the database.
     *
     * @param string $key The configuration key to set.
     * @param mixed $value The value to assign to the key.
     * @return void
     */
    public function set(string $key, mixed $value): void;

    /**
     * Set associative array of settings.
     * This method does not persist the value to the database.
     *
     * @param array $data
     *
     * Example Usage:
     * $settings->setMany([
     *     'some_key' => 'some_value',
     *     'another_key' => ['nested' => 'value'],
     * ]);
     *
     * @return void
     */
    public function setMany(array $data): void;

    /**
     * Save a configuration key and value to persistent storage.
     *
     * @param string $key The configuration key to save.
     * @param mixed $value The value to persist.
     * @return bool If saved or not
     */
    public function save(string $key, mixed $value);


    /**
     * Set or update multiple configuration keys with values.
     * Updates the in-memory config but doesn't immediately persist.
     *
     * @param array $data Associative array of key-value pairs to set.
     * i.e [
     *          'some_key' => 'some_value',
     *          'some_key2' => 'some_value2',
     *      ]
     * @return int Number of saved records
     */
    public function saveMany(array $data);
}
