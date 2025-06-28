<?php

declare(strict_types=1);

namespace Modules\Todo\Models;

use BitCore\Foundation\Database\Eloquent\SoftDeletes;
use BitCore\Application\Models\AppModel;

/**
 * This is the model class for table "groups".
 *
 * @property int $id
 * @property string $group_name
 * @property string $group_slug
 * @property int $clicks_generated
 * @property float $total_earnings
 * @property bool $is_auto_approved
 * @property float $default_commission_rate
 * @property int $commission_lock_period
 * @property string $reward_type
 * @property bool $is_enable_commission
 * @property float $commission_rate
 * @property string|null $commission_type
 * @property string|null $commission_rule
 * @property float $commission_amount
 * @property float $payout_minimum
 * @property \BitCore\Foundation\Carbon|null $created_at
 * @property \BitCore\Foundation\Carbon|null $updated_at
 * @property \BitCore\Foundation\Carbon|null $deleted_at
 */
class Group extends AppModel
{
    use SoftDeletes;

    protected $table = 'groups';

    protected $fillable = [
        'group_title',
        'group_description',
        'completed',
    ];

    protected $casts = [
        'completed' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get all affiliates belonging to this group.
     */
    public function affiliates()
    {
        return $this->hasManyRelation(Todo::class);
    }
}
