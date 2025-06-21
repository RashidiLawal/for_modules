<?php

declare(strict_types=1);

namespace BitCore\Application\Models;

use BitCore\Foundation\Database\Model;

/**
 * This is the model class for table "notification_templates".
 *
 * The followings are the available columns in table 'notification_templates':
 * @property integer $id
 * @property string $slug
 * @property string $channel
 * @property string $locale
 * @property string $subject
 * @property string $message
 * @property bool $active
 * @property string|\BitCore\Foundation\Carbon|null $created_at
 * @property string|\BitCore\Foundation\Carbon|null $updated_at
 */
class NotificationTemplate extends Model
{
    protected $fillable = ['slug', 'channel', 'locale', 'subject', 'message', 'active'];
}
