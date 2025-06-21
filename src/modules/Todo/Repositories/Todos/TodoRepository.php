<?php

declare(strict_types=1);

namespace Modules\Todo\Repositories\Todos;

use BitCore\Application\Repositories\AppRepository;
use BitCore\Foundation\Support\Str;
use Modules\Todo\Models\Todo;
use Modules\Todo\Repositories\Todos\TodoRepositoryInterface;

class TodoRepository extends AppRepository implements TodoRepositoryInterface
{
    // The todos table Model Class
    protected string $modelClass = Todo::class;

    /**
     * {@inheritDoc}
     */
    public function findBySlug(string $slug): ?Todo
    {
        return parent::findByColumn('todo_slug', $slug);
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data, array $options = []): ?Todo
    {
        if (empty($data['todo_slug']) && !empty($data['todo_title'])) {
            $data['todo_slug'] = Str::slug($data['todo_title']);
        }
        $todo = parent::create($data);
        return $todo;
    }

    /**
     * Check if a Todo with the given title already exists.
     *
     * This is useful for preventing duplicate Todos during creation or update.
     *
     * @param string $name The name of the Todo to check.
     * @return bool True if a Todo with the name exists, false otherwise.
     */
    public function nameExists(string $name): bool
    {
        return Todo::where('todo_title', $name)->exists();
    }

    /**
     * Export affiliates using optional date filters.
     *
     * @param array $filters
     * @return array
     */
    public function getAffiliatesForExport(array $filters): array
    {
        $query = Todo::query();

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('created_at', [$filters['start_date'], $filters['end_date']]);
        }

        return $query->get([
            'todo_title', 'todo_escreiption',
            'todo_slug', 'todo_description', 'todo_completed',
        ])->toArray();
    }

    /**
     * {@inheritDoc}
     */
    public function fetchAffiliates(
        array $queryParams
    ): array {
        $query = Todo::query();

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
