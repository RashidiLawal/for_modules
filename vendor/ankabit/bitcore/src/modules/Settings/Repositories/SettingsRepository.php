<?php

declare(strict_types=1);

namespace BitCore\Modules\Settings\Repositories;

use BitCore\Modules\Settings\Events\SettingsLoaded;
use BitCore\Application\Services\Settings\SystemConfig;

/**
 * Repository class for managing application settings via the database.
 *
 * Handles saving, registering, retrieving, and persisting settings
 * with associated metadata. Supports grouping of settings via group
 * and group ID for modular management.
 */
class SettingsRepository extends SystemConfig
{
    /**
     * The database table name where settings are stored.
     *
     * @var string
     */
    protected string $databaseTable = 'settings';

    /**
     * The currently active settings group.
     *
     * @var string
     */
    protected string $activeGroup = 'system';

    /**
     * The currently active settings group ID.
     *
     * @var string
     */
    protected string $activeGroupId = 'default';

    /**
     * The event class triggered when settings are loaded.
     *
     * @var class-string
     */
    protected string $eventClass = SettingsLoaded::class;

    /**
     * @inheritDoc
     */
    public function get(string $key, $default = null)
    {
        return $this->getRaw($key)['value'] ?? $default;
    }

    /**
     * Retrieve a setting's metadata by key.
     *
     * @param string $key     The setting key.
     * @return array
     */
    public function getMetadata(string $key): array
    {
        return $this->getRaw($key)['metadata'] ?? [];
    }

    /**
     * Register a new setting, optionally merging metadata if it exists.
     *
     * @param string $key            The setting key.
     * @param mixed  $defaultValue   The default value for the setting.
     * @param array  $metadata       Optional metadata for the setting.
     * @param bool   $overwriteValue Whether to overwrite an existing value.
     * @return bool                   If record saved or not
     */
    public function register(string $key, mixed $defaultValue, array $metadata = [], bool $overwriteValue = false): bool
    {
        return $this->registerMany(
            [
                $key => [
                    'value'    => $defaultValue,
                    'metadata' => $metadata,
                ]
            ],
            $overwriteValue
        ) == 1;
    }

    /**
     * Register multiple settings, merging existing metadata where applicable.
     *
     * @param array $data            Key-value array of settings with metadata.
     * @param bool  $overwriteValue  Whether to overwrite existing values.
     * @return int                   Number of affected rows.
     */
    public function registerMany(array $data, bool $overwriteValue = false): int
    {
        if (empty($data)) {
            return 0;
        }

        $updates = [];
        foreach ($data as $key => $item) {
            $_update = [
                'value'    => $item['value'] ?? null,
                'metadata' => $item['metadata'] ?? null
            ];

            $existing = $this->getRaw($key);
            if ($existing) {
                $currentMetadata = $this->maybeDecodeJson($existing['metadata']) ?? [];
                $mergedMetadata  = array_merge($currentMetadata, $_update['metadata'] ?? []);
                $_update['metadata'] = $mergedMetadata;

                if (!$overwriteValue) {
                    $_update['value'] = $existing['value'];
                }
            }

            $updates[$key] = $_update;
        }

        return $this->saveMany($updates, true);
    }

    /**
     * Set multiple settings in memory without persisting to the database.
     *
     * @param array $data               Key-value array of settings.
     * @param bool  $dataIsNormalized   Whether the provided data is normalized.
     * @return void
     */
    public function setMany(array $data, bool $dataIsNormalized = false): void
    {
        $normalizedData = $this->normalizeSettingsArray($data, $dataIsNormalized);

        foreach ($normalizedData as $key => $value) {
            $this->settings[$this->activeGroup][$this->activeGroupId][$key] = $value;
        }
    }

    /**
     * Persist multiple setting values to the database.
     *
     * @param array $data  Key-value array of settings.
     * @param bool  $dataIsNormalized   Whether the provided data is normalized.
     * @return int The number of saved records
     */
    public function saveMany(array $data, $dataIsNormalized = false): int
    {
        $this->setMany($data, $dataIsNormalized);
        return $this->persistMany($data, $dataIsNormalized);
    }

    /**
     * Load all settings from the database into local memory.
     *
     * @return self
     */
    public function load(): self
    {
        $result = db()->table($this->databaseTable)->get()->toArray();
        $normalizedSettings = $this->normalizeSettingsArray($result, true, true);
        $this->setSettings($normalizedSettings);
        return $this;
    }

    /**
     * Persist multiple key-value pairs to the database.
     *
     * @param array $data               Key-value array.
     * @param bool  $dataIsNormalized   Whether the provided data is normalized.
     * @return int Number of persisted data
     */
    protected function persistMany(array $data, bool $dataIsNormalized = false): int
    {
        if (empty($data)) {
            return 0;
        }

        $normalizedData = $this->normalizeSettingsArray($data, $dataIsNormalized);

        $updateValues = [];

        foreach ($normalizedData as $key => $item) {
            $updateValues[] = [
                'group'    => $this->activeGroup,
                'group_id' => $this->activeGroupId,
                'key'      => $key,
                'value'    => is_array($item['value']) || is_object($item['value'])
                    ? json_encode($item['value'])
                    : $item['value'],
                'metadata' => $item['metadata'] ? json_encode($item['metadata']) : null,
            ];
        }

        return db()->table($this->databaseTable)
            ->upsert($updateValues, ['key', 'group', 'group_id'], ['value', 'metadata']);
    }

    /**
     * Set the active group and group ID context.
     *
     * @param string     $group
     * @param string|int $groupId
     * @return self
     */
    public function setActiveGroup(string $group, string|int $groupId): self
    {
        $this->activeGroup = $group;
        $this->activeGroupId = (string)$groupId;
        return $this;
    }

    /**
     * Clone this repository instance with a different group and group ID.
     *
     * @param string     $group
     * @param string|int $groupId
     * @return self
     */
    public function useGroup(string $group, string|int $groupId): self
    {
        $instance = new static($this->getAll());
        return $instance->setActiveGroup($group, $groupId);
    }

    /**
     * Retrieve raw setting data by key, including value and metadata.
     *
     * @param string $key
     * @return array|null
     */
    protected function getRaw(string $key): ?array
    {
        return $this->settings[$this->activeGroup][$this->activeGroupId][$key] ?? null;
    }

    /**
     * Attempt to decode a JSON string, returning original value if invalid.
     *
     * @param mixed $value
     * @return mixed
     */
    protected function maybeDecodeJson(mixed $value): mixed
    {
        if (!$value) {
            return $value;
        }
        if (!is_string($value) || strpos($value, '{') === false) {
            return $value;
        }

        $decoded = json_decode($value, true);
        return (json_last_error() === JSON_ERROR_NONE) ? $decoded : $value;
    }

    /**
     * Normalize a settings array structure for persistence or retrieval.
     *
     * @param array $data               The raw or partially processed data.
     * @param bool  $dataIsNormalized   Whether the input data is already normalized.
     * @param bool  $fromDatabase       Whether the input data came from the database.
     * @return array                    The normalized array structure.
     */
    protected function normalizeSettingsArray(
        array $data,
        bool $dataIsNormalized = false,
        bool $fromDatabase = false
    ): array {
        $normalized = [];

        foreach ($data as $key => $value) {
            if ($fromDatabase) {
                /** @var object $value */
                $setting = $value;

                $normalized[$setting->group][$setting->group_id][$setting->key] = [
                    'value'    => $this->maybeDecodeJson($setting->value),
                    'metadata' => $this->maybeDecodeJson($setting->metadata),
                ];
            } else {
                $normalized[$key] = $dataIsNormalized
                    ? $value
                    : ['value' => $value, 'metadata' => null];
            }
        }

        return $normalized;
    }
}
