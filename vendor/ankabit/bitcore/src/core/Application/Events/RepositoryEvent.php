<?php

declare(strict_types=1);

namespace BitCore\Application\Events;

/**
 * Class RepositoryEvent
 *
 * This event is dispatched whenever a repository performs an action (such as creating,
 * updating, or deleting) on a model. The event includes contextual information about
 * the repository class, the action being performed, and additional data encapsulated
 * within a payload array.
 *
 * The repository class is passed as a string (class name) to provide context on where
 * the event originated, while the action indicates the operation being carried out.
 *
 * The payload contains additional data, such as the model instance, model ID, update data,
 * or other relevant context depending on the event.
 *
 * This event is useful for logging, auditing, or triggering other actions based on
 * repository activities.
 *
 * @package BitCore\Application\Events
 */
class RepositoryEvent extends GenericEvent
{
    /**
     * Fired before a single record is retrieved using find.
     */
    public const BEFORE_FIND = 'repository:beforeFind';

    /**
     * Fired after a single record is retrieved using find.
     */
    public const AFTER_FIND  = 'repository:afterFind';

    /**
     * Fired before records are fetched from the data source.
     */
    public const BEFORE_FETCH = 'repository:beforeFetch';

    /**
     * Fired right before the fetch operation is executed.
     * Allows final query adjustments.
     */
    public const WILL_FETCH = 'repository:willFetch';

    /**
     * Fired after records are fetched from the data source.
     */
    public const AFTER_FETCH = 'repository:afterFetch';

    /**
     * Fired before a new record is created.
     */
    public const BEFORE_CREATE = 'repository:beforeCreate';

    /**
     * Fired after a new record is created.
     */
    public const AFTER_CREATE = 'repository:afterCreate';

    /**
     * Fired before inserting a batch of records.
     */
    public const BEFORE_BATCH_INSERT = 'repository:beforeBatchInsert';

    /**
     * Fired after a batch of records has been inserted.
     */
    public const AFTER_BATCH_INSERT = 'repository:afterBatchInsert';

    /**
     * Fired before a record is updated.
     */
    public const BEFORE_UPDATE = 'repository:beforeUpdate';

    /**
     * Fired just before the update operation is executed.
     */
    public const WILL_UPDATE = 'repository:willUpdate';

    /**
     * Fired after a record is updated.
     */
    public const AFTER_UPDATE = 'repository:afterUpdate';

    /**
     * Fired before a record is deleted.
     */
    public const BEFORE_DELETE = 'repository:beforeDelete';

    /**
     * Fired just before the delete operation is executed.
     */
    public const WILL_DELETE = 'repository:willDelete';

    /**
     * Fired after a record is deleted.
     */
    public const AFTER_DELETE = 'repository:afterDelete';

    /**
     * Fired before a bulk delete operation is executed.
     */
    public const BEFORE_BULK_DELETE = 'repository:beforeBulkDelete';

    /**
     * Fired after a bulk delete operation is completed.
     */
    public const AFTER_BULK_DELETE = 'repository:afterBulkDelete';

    /**
     * Fired just before the count operation is executed.
     */
    public const WILL_COUNT = 'repository:willCount';
}
