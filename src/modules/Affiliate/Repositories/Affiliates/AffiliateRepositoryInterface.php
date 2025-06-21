<?php

declare(strict_types=1);

namespace Modules\Affiliate\Repositories\Affiliates;

use BitCore\Application\Repositories\AppRepositoryInterface;
use Modules\Affiliate\Models\Affiliate;

/**
 * Interface AffiliateRepositoryInterface
 *
 * Defines the contract for interacting with Affiliate data.
 */
interface AffiliateRepositoryInterface extends AppRepositoryInterface
{
    /**
     * Find a affiliate type by its ID.
     *
     * @param int $id The ID of the affiliate type.
     * @return Affiliate|null The affiliate type if found, null otherwise.
     */
    public function findById(int $id);

    /**
     * Find a tag by its slug.
     *
     * @param string $slug
     * @return Affiliate|null
     */
    public function findBySlug(string $slug): ?Affiliate;

    /**
     * Create a new affiliate with the provided data.
     *
     * @param array $data Key-value pairs representing the affiliate fields.
     * @return Affiliate The created affiliate model instance.
     */
    public function create(array $data);

    /**
     * Update an existing affiliate with the provided data.
     *
     * @param int $id The affiliate instance to update.
     * @param array $data Key-value pairs of fields to update.
     * @return Affiliate True if the update was successful, false otherwise.
     */
    public function update(int $id, array $data);

    /**
     * Check if a Affiliate with the given name already exists.
     *
     * This is useful for preventing duplicate Affiliates during creation or update.
     *
     * @param string $name The name of the Affiliate to check.
     * @return bool True if a Affiliate with the name exists, false otherwise.
     */
    public function nameExists(string $name): bool;

    /**
     * Export affiliates using optional filters like date ranges.
     *
     * @param array $filters Filters such as ['start_date' => ..., 'end_date' => ...].
     * @return array Array of filtered affiliate data ready for export.
     */
    public function getAffiliatesForExport(array $filters): array;

    /**
     * Fetch paginated and filtered list of affiliates.
     * @param array       $queryParams    Associative array of column-based filters.
     * @return array {
     *     @type array $data  The list of affiliates.
     *     @type int   $total Total number of matching affiliates.
     * }
     */
    public function fetchAffiliates(
        array $queryParams
    ): array;
}
