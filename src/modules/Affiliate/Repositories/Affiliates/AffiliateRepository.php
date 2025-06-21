<?php

declare(strict_types=1);

namespace Modules\Affiliate\Repositories\Affiliates;

use BitCore\Application\Repositories\AppRepository;
use BitCore\Foundation\Support\Str;
use Modules\Affiliate\Models\Affiliate;
use Modules\Affiliate\Repositories\Affiliates\AffiliateRepositoryInterface;

class AffiliateRepository extends AppRepository implements AffiliateRepositoryInterface
{
    protected string $modelClass = Affiliate::class;

    /**
     * {@inheritDoc}
     */
    public function findBySlug(string $slug): ?Affiliate
    {
        return parent::findByColumn('affiliate_slug', $slug);
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data, array $options = []): ?Affiliate
    {
        if (empty($data['affiliate_slug']) && !empty($data['affiliate_name'])) {
            $data['affiliate_slug'] = Str::slug($data['affiliate_name']);
        }
        $affiliate = parent::create($data);
        return $affiliate;
    }

    /**
     * Check if a Affiliate with the given name already exists.
     *
     * This is useful for preventing duplicate Affiliates during creation or update.
     *
     * @param string $name The name of the Affiliate to check.
     * @return bool True if a Affiliate with the name exists, false otherwise.
     */
    public function nameExists(string $name): bool
    {
        return Affiliate::where('affiliate_name', $name)->exists();
    }

    /**
     * Export affiliates using optional date filters.
     *
     * @param array $filters
     * @return array
     */
    public function getAffiliatesForExport(array $filters): array
    {
        $query = Affiliate::query();

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('created_at', [$filters['start_date'], $filters['end_date']]);
        }

        return $query->get([
            'name', 'email', 'commission_type', 'commission_rate',
            'status', 'approved_at', 'created_at'
        ])->toArray();
    }

    /**
     * {@inheritDoc}
     */
    public function fetchAffiliates(
        array $queryParams
    ): array {
        $query = Affiliate::query();

        // Eager-load related models such as group, referral links, etc.
        $query->with(['group']);

        // Apply dynamic filters (e.g., title, status, etc.)
        $options = [
            'query' => $query,
        ];

        // Use fetchWithFilters for filtering, search, pagination, and sorting
        return $this->fetchWithFilters($queryParams, $options);
    }
}
