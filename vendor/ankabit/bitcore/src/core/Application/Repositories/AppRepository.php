<?php

declare(strict_types=1);

namespace BitCore\Application\Repositories;

use BitCore\Application\Events\RepositoryEvent;
use BitCore\Application\Models\AppModel;
use RuntimeException;

abstract class AppRepository implements AppRepositoryInterface
{
    protected string $modelClass;

    /**
     * BaseRepository constructor.
     *
     * @param string|null $modelClass The fully qualified class name of the model.
     *
     * @throws RuntimeException If no model class is provided.
     */
    public function __construct(?string $modelClass = null)
    {
        // Assign model class if provided
        if (!empty($modelClass)) {
            $this->modelClass = $modelClass;
        }

        // Ensure the model class is set before proceeding
        if (empty($this->modelClass)) {
            // @todo Translate message
            throw new RuntimeException(
                'A valid model class must be specified for the repository.'
            );
        }
    }

    /**
     * Dispatches a repository event with the specified action and payload.
     *
     * This method creates a new RepositoryEvent instance with the given action and payload,
     * ensuring the payload includes the model class if not already set. The event is then
     * dispatched through the hooks system, allowing registered listeners to respond.
     *
     * @param string $action The event action to dispatch (e.g., RepositoryEvent::BEFORE_DELETE).
     * @param array $payload Optional data to include in the event payload. If 'modelClass' is not set,
     *                       it will be populated with the repository's model class.
     * @return RepositoryEvent The dispatched event instance, which may contain updated payload or results.
     */
    final protected function dispatchEvent($action, array $payload = [])
    {
        if (!isset($payload['modelClass'])) {
            $payload['modelClass'] = $this->modelClass;
        }

        $event = new RepositoryEvent(
            static::class,
            $action,
            $payload
        );

        //hooks()->dispatch(RepositoryEvent::class, $event);
        hooks()->dispatch($action, $event);

        return $event;
    }

    /**
     * Registers a listener for a specific repository event action.
     *
     * This method attaches a callback to the specified event action, which will be executed
     * when the corresponding event is dispatched via the hooks system.
     *
     * @param string $action The event action to listen for (e.g., RepositoryEvent::BEFORE_DELETE).
     * @param callable $callback The callback function to execute when the event is triggered.
     * @return void
     */
    final protected function listenEvent($action, callable $callback)
    {
        hooks()->listen($action, $callback);
    }

    /**
     * {@inheritDoc}
     */
    public function count(array $filters = []): int
    {
        $query = $this->modelClass::query();

        $event = $this->dispatchEvent(RepositoryEvent::WILL_COUNT, [
            'filters' => $filters,
            'query'   => $query
        ]);

        if (!$event->shouldContinue()) {
            return (int)$event->getResult();
        }

        /** @var AppModel|object */
        $query =  $event->getPayloadValue('query');

        /** @var array */
        $filters =  $event->getPayloadValue('filters');

        // Apply filters if provided
        foreach ($filters as $column => $value) {
            $query->where($column, $value);
        }

        return $query->count();
    }

    /**
     * {@inheritDoc}
     */
    public function findById(int $id)
    {
        return $this->findByColumn('id', $id);
    }

    /**
     * {@inheritDoc}
     */
    public function findByColumn(string $column, mixed $value)
    {
        // Allow query params and option modification
        $event = $this->dispatchEvent(RepositoryEvent::BEFORE_FIND, [
            'column' => $column,
            'value'     => $value,
        ]);

        if (!$event->shouldContinue()) {
            return $event->getResult();
        }

        $column = $event->getPayloadValue('column');
        $value = $event->getPayloadValue('value');

        /** @var AppModel|object|null */
        $model = $this->modelClass::where($column, $value)->first();

        // Allow query params and option modification
        $event = $this->dispatchEvent(RepositoryEvent::AFTER_FETCH, [
            'column' => $column,
            'value'     => $value,
            'model' => $model
        ]);

        if (!$event->shouldContinue()) {
            return $event->getResult();
        }

        /** @var AppModel|object|null */
        $model = $event->getPayloadValue('model');

        return $model;
    }

    /**
     * {@inheritDoc}
     */
    public function fetchWithFilters(
        array $queryParams = [],
        array $options = []
    ): array {

        // Allow query params and option modification
        $event = $this->dispatchEvent(RepositoryEvent::BEFORE_FETCH, [
            'queryParams' => $queryParams,
            'options'     => $options,
        ]);

        if (!$event->shouldContinue()) {
            return (array)$event->getResult();
        }

        $queryParams = $event->getPayloadValue('queryParams', []);
        $options = $event->getPayloadValue('options', []);

        $page      = (int) ($queryParams['page'] ?? 1);
        $perPage   = (int) ($queryParams['per_page'] ?? 10);
        $filters   = $queryParams['filters'] ?? [];
        $sortBy    = $queryParams['sort_by'] ?? 'created_at';
        $search    = $queryParams['search'] ?? null;
        $sortOrder = $queryParams['order'] ?? 'desc';

        $model = $options['model'] ?? $this->modelClass;
        $model = is_string($model) ? new $model() : $model;

        $query = $options['query'] ?? $model->newQuery();
        $fillable = $model->getFillable();

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

        // Sorting
        if ($sortBy && in_array($sortBy, $fillable)) {
            $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';
            $query->orderBy($sortBy, $sortOrder);
        }

        // Allow further query modification
        $event = $this->dispatchEvent(RepositoryEvent::WILL_FETCH, [
            'query'          => $query,
            'model'          => $model,
            'queryParams' => $queryParams,
            'options'     => $options,
        ]);

        if (!$event->shouldContinue()) {
            return (array)$event->getResult();
        }

        $query = $event->getPayloadValue('query');

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

        // Response event
        $event = $this->dispatchEvent(RepositoryEvent::AFTER_FETCH, [
            'response'        => $response,
            'query'          => $query,
            'model'          => $model,
            'queryParams' => $queryParams,
            'options'     => $options,
        ]);

        if (!$event->shouldContinue()) {
            return (array)$event->getResult();
        }

        return $event->getPayloadValue('response');
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data, array $options = [])
    {
        // Notify before creating
        $event = $this->dispatchEvent(
            RepositoryEvent::BEFORE_CREATE,
            ['data' => $data, 'options' => $options]
        );

        if (!$event->shouldContinue()) {
            return $event->getResult();
        }

        $data = $event->getPayloadValue('data');

        // Create the record
        $model = $this->modelClass::create($data);

        // Notify about the creation
        $event = $this->dispatchEvent(
            RepositoryEvent::AFTER_CREATE,
            [
                'data' => $data,
                'model' => $model,
                'created' => !empty($model)
            ]
        );

        if (!$event->shouldContinue()) {
            return $event->getResult();
        }

        return $event->getPayloadValue('model');
    }

    /**
     * {@inheritDoc}
     */
    public function insertBatch(array $data): int
    {
        if (empty($data)) {
            return 0;
        }

        $chunkSize = 500;

        // Notify before starting batch insert
        $event = $this->dispatchEvent(RepositoryEvent::BEFORE_BATCH_INSERT, [
            'data' => $data,
            'chunkSize' => $chunkSize
        ]);

        if (!$event->shouldContinue()) {
            return (int)$event->getResult();
        }

        $data = (array)$event->getPayloadValue('data');
        $chunkSize = (int)$event->getPayloadValue('chunkSize');

        $chunkedDatas = array_chunk($data, $chunkSize); // Adjust batch size if needed
        $totalInserted = 0;

        foreach ($chunkedDatas as $batch) {
            $this->modelClass::insert($batch);
            $totalInserted += count($batch);
        }

        // Notify after completing all inserts
        $event = $this->dispatchEvent(RepositoryEvent::AFTER_BATCH_INSERT, [
            'data' => $data,
            'totalInserted' => $totalInserted
        ]);

        if (!$event->shouldContinue()) {
            return (int)$event->getResult();
        }

        return (int)$event->getPayloadValue('totalInserted');
    }

    /**
     * {@inheritDoc}
     */
    public function update(int $id, array $data)
    {
        // Notify before update attempt
        $event = $this->dispatchEvent(
            RepositoryEvent::BEFORE_UPDATE,
            ['id' => $id, 'model' => null]
        );

        if (!$event->shouldContinue()) {
            return $event->getResult();
        }

        $model = $this->findById($id);
        if (!$model) {
            return null;
        }

        // Notify before actual update
        $event = $this->dispatchEvent(
            RepositoryEvent::WILL_UPDATE,
            [
                'id' => $id,
                'model' => $model,
                'data' => $data
            ]
        );

        if (!$event->shouldContinue()) {
            return $event->getResult();
        }

        // Update payload refs
        $data = $event->getPayloadValue('data');
        $model = $event->getPayloadValue('model');
        if (!$model) {
            return null;
        }

        // Make actual update
        $updated = $model->update($data);
        if ($updated) {
            // @todo Look into this for possibily saving this query
            $updated = $model->fresh();
        }

        // Notify about the update
        $event = $this->dispatchEvent(
            RepositoryEvent::AFTER_UPDATE,
            [
                'id' => $id,
                'modelClass' => $this->modelClass,
                'model' => $model,
                'data' => $data,
                'updated' => $updated
            ]
        );

        if (!$event->shouldContinue()) {
            return $event->getResult();
        }

        return $event->getPayloadValue('model');
    }

    /**
     * {@inheritDoc}
     */
    public function updateModel(AppModel $model, array $data)
    {
        // Notify before update attempt
        $event = $this->dispatchEvent(
            RepositoryEvent::BEFORE_UPDATE,
            ['id' => $model->id, 'model' => $model]
        );

        if (!$event->shouldContinue()) {
            return $event->getResult();
        }

        // Notify before actual update
        $event = $this->dispatchEvent(
            RepositoryEvent::WILL_UPDATE,
            [
                'id' => $model->id,
                'model' => $model,
                'data' => $data
            ]
        );

        if (!$event->shouldContinue()) {
            return $event->getResult();
        }

        // Update payload refs
        $data = $event->getPayloadValue('data');
        $model = $event->getPayloadValue('model');
        if (!$model) {
            return null;
        }

        // Make actual update
        $updated = $model->update($data);

        // Notify about the update
        $event = $this->dispatchEvent(
            RepositoryEvent::AFTER_UPDATE,
            [
                'id' => $model->id,
                'modelClass' => $this->modelClass,
                'model' => $model,
                'data' => $data,
                'updated' => $updated
            ]
        );

        if (!$event->shouldContinue()) {
            return $event->getResult();
        }

        return $event->getPayloadValue('model');
    }

    /**
     * {@inheritDoc}
     */
    public function delete(int $id): bool
    {
        // Notify before deleting
        $event = $this->dispatchEvent(
            RepositoryEvent::BEFORE_DELETE,
            ['id' => $id]
        );

        if (!$event->shouldContinue()) {
            return (bool)$event->getResult();
        }

        // Ensure $id is up to date
        $id = (int)$event->getPayloadValue('id');

        // Lookup
        $model = $this->findById($id);

        if (!$model) {
            return false;
        }

        return $this->deleteModel($model);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteModel(AppModel $model): bool
    {
        // Notify before actual deletion
        $event = $this->dispatchEvent(
            RepositoryEvent::WILL_DELETE,
            [
                'model' => $model,
            ]
        );

        if (!$event->shouldContinue()) {
            return (bool)$event->getResult();
        }

        // Ensure $model updated for filter purposes
        $model = $event->getPayloadValue('model');

        // Make actually deletion if $model still available
        $deleted =  $model ? $model->delete() : false;

        // Notify about the deletion
        $event = $this->dispatchEvent(
            RepositoryEvent::AFTER_DELETE,
            [
                'model' => $model,
                'deleted' => $deleted
            ]
        );

        if (!$event->shouldContinue()) {
            return (bool)$event->getResult();
        }

        return (bool)$event->getPayloadValue('deleted');
    }

    /**
     * {@inheritDoc}
     */
    public function bulkDelete(array $ids): int
    {
        if (empty($ids)) {
            return 0;
        }

        // Notify before bulk delete
        $event = $this->dispatchEvent(RepositoryEvent::BEFORE_BULK_DELETE, [
            'ids' => $ids
        ]);

        if (!$event->shouldContinue()) {
            return (int)$event->getResult();
        }

        // Reassign in case listener modified the array
        $ids = $event->getPayloadValue('ids');

        $deletedCount = $this->modelClass::destroy($ids);

        // Notify after bulk delete
        $event = $this->dispatchEvent(RepositoryEvent::AFTER_BULK_DELETE, [
            'ids' => $ids,
            'deletedCount' => $deletedCount
        ]);

        if (!$event->shouldContinue()) {
            return (int)$event->getResult();
        }

        return (int)$event->getPayloadValue('deletedCount');
    }
}
