<?php

declare(strict_types=1);

namespace Modules\Affiliate\Models;

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
        'group_name',
        'group_slug',
        'clicks_generated',
        'total_earnings',
        'is_auto_approved',
        'default_commission_rate',
        'commission_lock_period',
        'reward_type',
        'is_enable_commission',
        'commission_rate',
        'commission_type',
        'commission_rule',
        'commission_amount',
        'payout_minimum',
    ];

    protected $casts = [
        'total_earnings'           => 'decimal:2',
        'default_commission_rate'  => 'decimal:2',
        'commission_rate'          => 'decimal:2',
        'commission_amount'        => 'decimal:2',
        'payout_minimum'           => 'decimal:2',
        'is_auto_approved'         => 'boolean',
        'is_enable_commission'     => 'boolean',
    ];

    /**
     * Get all affiliates belonging to this group.
     */
    public function affiliates()
    {
        return $this->hasMany(Affiliate::class);
    }
}
