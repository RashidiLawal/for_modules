<?php

declare(strict_types=1);

namespace Modules\Todo\Repositories\Groups;

use BitCore\Application\Repositories\AppRepository;
use Modules\Todo\Models\Group;
use Modules\Todo\Repositories\Groups\GroupRepositoryInterface;

class GroupRepository extends AppRepository implements GroupRepositoryInterface
{
    protected string $modelClass = Group::class;
    /**
     * Find a tag by its slug.
     *
     * @param string $slug
     * @return Group|null
     */
    public function findBySlug(string $slug): ?Group
    {
        return parent::findByColumn('group_slug', $slug);
    }

    /**
     * Check if a Group with the given name already exists.
     *
     * This is useful for preventing duplicate Groups during creation or update.
     *
     * @param string $name The name of the Group to check.
     * @return bool True if a Group with the name exists, false otherwise.
     */
    public function nameExists(string $name): bool
    {
        return Group::where('group_name', $name)->exists();
    }

    /**
     * Create a new Group.
     */
    public function create(array $data, array $options = []): Group
    {
        if (empty($data['group_slug']) && !empty($data['group_name'])) {
            $data['group_slug'] = \BitCore\Foundation\Support\Str::slug($data['group_name']);
        }
        return parent::create($data);
    }

    /**
     * Fetch groups for export using optional date filters.
     */
    public function getGroupsForExport(array $filters): array
    {
        $query = Group::query();

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('created_at', [$filters['start_date'], $filters['end_date']]);
        }

        return $query->get(['group_name', 'created_at'])->toArray();
    }
}
