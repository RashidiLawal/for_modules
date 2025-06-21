<?php
declare(strict_types=1);

namespace Modules\Todo\Models;

use BitCore\Foundation\Database\Eloquent\SoftDeletes;
// use BitCore\Foundation\Database\Model;
use BitCore\Application\Models\AppModel;

/**
 * This is the model class for table "todos".
 *
 * The followings are the available columns in table 'todos':
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property boolean $completed
 * @property string|\BitCore\Foundation\Carbon|null $created_at
 * @property string|\BitCore\Foundation\Carbon|null $updated_at
 * @property string|\BitCore\Foundation\Carbon|null $deleted_at
 */
class Todo extends AppModel
{
    use SoftDeletes;

    protected $fillable = ['todo_title', 'todo_description', 'todo_completed', 'created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'completed' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
    /**
     * Get the completed status as a boolean.
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return (bool) $this->completed;
    }

    /**
     * Set the completed status.
     *
     * @param bool $value
     */
    public function setCompleted(bool $value): void
    {
        $this->completed = $value;
    }
}