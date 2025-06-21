<?php

declare(strict_types=1);

namespace BitCore\Application\Models;

use BitCore\Foundation\Database\Eloquent\Concerns\Relationships;
use BitCore\Foundation\Database\Model;

class AppModel extends Model implements AppModelInterface
{
    use Relationships;

    public static function fetchWithFilters(
        array $filters = [],
        int $page = 1,
        int $perPage = 20,
        ?string $sortBy = null,
        ?string $sortOrder = 'ASC',
        ?string $search = null,
        array $options = []
    ): array {
        $model = $options['model'] ?? new static();
        $instance = is_string($model) ? new $model() : $model;

        $query = $options['query'] ?? $instance->newQuery();
        $fillable = $instance->getFillable();

        // Eager load relationships
        if (!empty($options['with'])) {
            $query->with($options['with']);
        }

        // Select specific columns
        if (!empty($options['select'])) {
            $query->select($options['select']);
        }

        // Global Search
        if ($search) {
            $query->where(function ($q) use ($search, $fillable) {
                foreach ($fillable as $column) {
                    $q->orWhere($column, 'LIKE', "%{$search}%");
                }
            });
        }

        // Field-level filtering
        foreach ($filters as $column => $value) {
            if (in_array($column, $fillable)) {
                $query->where($column, 'LIKE', "%{$value}%");
            }
        }

        // Custom query callback
        if (!empty($options['customize']) && is_callable($options['customize'])) {
            $options['customize']($query);
        }

        // Sorting
        if ($sortBy && in_array($sortBy, $fillable)) {
            $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';
            $query->orderBy($sortBy, $sortOrder);
        }

        $total = $query->count();
        $results = $query->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get()
            ->toArray(); //Convert to array so hook can modify it

        // Initial response
        $response = [
            'data' => $results,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage,
                'total_pages' => ceil($total / $perPage),
            ]
        ];

        return $response;
    }

    public static function createRecord(array $data, array $options = [])
    {
        $model = $options['model'] ?? new static();
        $instance = is_string($model) ? new $model() : $model;

        // Optionally mutate data before creation
        if (!empty($options['beforeCreate']) && is_callable($options['beforeCreate'])) {
            $data = $options['beforeCreate']($data);
        }

        // Create the record
        $record = $instance::create($data);

        // Handle relations
        if (!empty($options['relations']) && is_array($options['relations'])) {
            foreach ($options['relations'] as $relation => $handler) {
                if (is_callable($handler)) {
                    // Closure can handle custom logic
                    $handler($record);
                } elseif (is_array($handler)) {
                    // Basic relation logic using array structure
                    $ids = $handler['ids'] ?? [];
                    $pivot = $handler['pivot'] ?? [];

                    if (!empty($ids)) {
                        if (!empty($pivot)) {
                            $record->{$relation}()->attach($pivot);
                        } else {
                            $record->{$relation}()->attach($ids);
                        }
                    }
                }
            }
        }

        return $record;
    }

    public static function updateRecord($model, array $data, array $options = [])
    {
        // Run beforeUpdate hook if provided
        if (!empty($options['beforeUpdate']) && is_callable($options['beforeUpdate'])) {
            $data = $options['beforeUpdate']($data, $model);
        }

        // Perform update
        $updated = $model->update($data);

        // Handle relation sync if defined
        if (!empty($options['relations']) && is_array($options['relations'])) {
            foreach ($options['relations'] as $relation => $relationData) {
                if (method_exists($model, $relation)) {
                    $pivotData = is_array($relationData) ? $relationData : [];

                    // Sync relation with optional pivot data
                    $model->$relation()->sync($pivotData);
                }
            }
        }

        // Run afterUpdate hook if provided
        if (!empty($options['afterUpdate']) && is_callable($options['afterUpdate'])) {
            $options['afterUpdate']($model);
        }

        return $updated ? $model->fresh() : null;
    }

    public static function deleteRecord($modelOrIds, string $modelClass = null): bool
    {
        // Handle single model instance
        if (is_object($modelOrIds)) {
            return $modelOrIds->delete();
        }

        // Handle bulk delete
        if (is_array($modelOrIds) && $modelClass) {
            return $modelClass::whereIn('id', $modelOrIds)->delete() > 0;
        }

        // Invalid usage
        return false;
    }
}
