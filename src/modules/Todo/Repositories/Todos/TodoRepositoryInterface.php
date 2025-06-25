<?php

declare(strict_types=1);


namespace Modules\Todo\Repositories\Todos;

use BitCore\Application\Repositories\AppRepositoryInterface;
use Modules\Todo\Models\Todo;


/**
 * Interface TodoRepositoryInterface
 *
 * Defines the contract for interacting with Todo data.
 */
interface TodoRepositoryInterface extends AppRepositoryInterface
{
    /**
     * Find a todo type by its ID.
     *
     * @param int $id The ID of the todo type.
     * @return Todo|null The todo type if found, null otherwise.
     */
    public function findById(int $id);

    /**
     * Find a tag by its slug.
     *
     * @param string $slug
     * @return Todo|null
     */
    public function findBySlug(string $slug): ?Todo;

    /**
     * Create a new todo with the provided data.
     *
     * @param array $data Key-value pairs representing the todo fields.
     * @return Todo The created todo model instance.
     */
    public function create(array $data);

    /**
     * Update an existing todo with the provided data.
     *
     * @param int $id The todo instance to update.
     * @param array $data Key-value pairs of fields to update.
     * @return Todo True if the update was successful, false otherwise.
     */
    public function update(int $id, array $data);

    /**
     * Check if a Todo with the given name already exists.
     *
     * This is useful for preventing duplicate Todos during creation or update.
     *
     * @param string $name The name of the Todo to check.
     * @return bool True if a Todo with the name exists, false otherwise.
     */
    public function nameExists(string $name): bool;

    /**
     * Export todos using optional filters like date ranges.
     *
     * @param array $filters Filters such as ['start_date' => ..., 'end_date' => ...].
     * @return array Array of filtered todo data ready for export.
     */
    public function getTodosForExport(array $filters): array;

    /**
     * Fetch paginated and filtered list of todoS.
     * @param array       $queryParams    Associative array of column-based filters.
     * @return array {
     *     @type array $data  The list of todos.
     *     @type int   $total Total number of matching todos.
     * }
     */
    public function fetchTodos(
        array $queryParams
    ): array;
}
