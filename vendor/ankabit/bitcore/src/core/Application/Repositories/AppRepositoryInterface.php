<?php

declare(strict_types=1);

namespace BitCore\Application\Repositories;

use BitCore\Application\Models\AppModel;

interface AppRepositoryInterface
{
    /**
     * Count the number of record based on optional filters.
     *
     * @param array $filters Optional filters (e.g., ['status' => 'active']).
     * @return int The number of records matching the filters.
     */
    public function count(array $filters = []): int;

    /**
     * Find a record by a given ID
     *
     * @param integer $id
     * @return AppModel|object|null
     */
    public function findById(int $id);

    /**
     * Find a record by a given column and value.
     *
     * @param string $column The column name to filter by.
     * @param mixed  $value  The value to match in the column.
     * @return AppModel|object|null
     */
    public function findByColumn(string $column, mixed $value);

    /**
     * Fetch with dynamic filtering, pagination, and sorting.
     *
     * @param array $queryParams
     * @param array $options
     * @return array
     */
    public function fetchWithFilters(
        array $queryParams = [],
        array $options = []
    ): array;

    /**
     * Create a new model entry.
     *
     * @param array $data
     * @return AppModel|object|null
     */
    public function create(array $data);

    /**
     * Bulk insert data into the database efficiently using batch insert.
     *
     * @param array $data
     * @return int Number of rows inserted.
     */
    public function insertBatch(array $data): int;

    /**
     * Update an existing model entry by its ID.
     *
     * @param int $id
     * @param array $data
     * @return AppModel|object|null
     */
    public function update(int $id, array $data);

    /**
     * Update model from instance directly.
     * This is preferred over update() when the model instance is available.
     *
     * @param AppModel $model
     * @param array $data
     * @return AppModel|object|null
     */
    public function updateModel(AppModel $model, array $data);

    /**
     * Delete a data by its unique ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Delete data by its model instance.
     *
     * @param AppModel $model
     * @return bool
     */
    public function deleteModel(AppModel $model): bool;

    /**
     * Bulk delete data by their IDs.
     *
     * @param array $ids
     * @return int The total number of items deleted
     */
    public function bulkDelete(array $ids): int;
}
