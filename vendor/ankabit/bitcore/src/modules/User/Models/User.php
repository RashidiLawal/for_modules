<?php

declare(strict_types=1);

namespace BitCore\Modules\User\Models;

use BitCore\Foundation\Database\Eloquent\SoftDeletes;
use BitCore\Foundation\Database\Model;

/**
 * This is the model class for table "users".
 *
 * The followings are the available columns in table 'users':
 * @property integer $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $password
 * @property string|\BitCore\Foundation\Carbon|null $created_at
 * @property string|\BitCore\Foundation\Carbon|null $updated_at
 * @property string|\BitCore\Foundation\Carbon|null $deleted_at
 */
class User extends Model
{
    use SoftDeletes;

    protected $fillable = ['first_name', 'last_name', 'email', 'password'];
}
