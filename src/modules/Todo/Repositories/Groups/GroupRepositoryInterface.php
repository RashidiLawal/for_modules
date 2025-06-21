<?php

declare(strict_types=1);

namespace Modules\Todo\Repositories\Groups;

use BitCore\Application\Repositories\AppRepositoryInterface;
use Modules\Todo\Models\Group;

/**
 * Interface GroupRepositoryInterface
 *
 * Defines the contract for interacting with Group data.
 */
interface GroupRepositoryInterface extends AppRepositoryInterface
{
    /**
     * Find a group by its unique ID.
     *
     * @param int $id The group ID.
     * @return Group|null The found group or null if not found.
     */
    public function findById(int $id);

    /**
     * Find a tag by its slug.
     *
     * @param string $slug
     * @return Group|null
     */
    public function findBySlug(string $slug): ?Group;

    /**
     * Create a new group with the provided data.
     *
     * @param array $data Key-value pairs representing the group fields.
     * @return Group The created group model instance.
     */
    public function create(array $data);

    /**
     * {@inheritDoc}
     * @return Group|null
     */
    public function update(int $id, array $data);

    /**
     * Check if a Group with the given name already exists.
     *
     * This is useful for preventing duplicate Groups during creation or update.
     *
     * @param string $name The name of the Group to check.
     * @return bool True if a Group with the name exists, false otherwise.
     */
    public function nameExists(string $name): bool;

    /**
     * Export groups using optional filters like date ranges.
     *
     * @param array $filters Filters such as ['start_date' => ..., 'end_date' => ...].
     * @return array Array of filtered group data ready for export.
     */
    public function getGroupsForExport(array $filters): array;
}
