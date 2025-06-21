<?php

declare(strict_types=1);

namespace Modules\Affiliate\Models;

use BitCore\Foundation\Database\Eloquent\SoftDeletes;
use BitCore\Application\Models\AppModel;

/**
 * This is the model class for table "affiliates".
 *
 * @property int $id
 * @property string $affiliate_name
 * @property string $affiliate_slug
 * @property string|null $referral_link
 * @property string $status
 * @property int $clicks_generated
 * @property float $earnings
 * @property string|null $payout_date
 * @property string $payout_status
 * @property int $total_sales
 * @property float $commission
 * @property int $group_id
 * @property \BitCore\Foundation\Carbon|null $created_at
 * @property \BitCore\Foundation\Carbon|null $updated_at
 * @property \BitCore\Foundation\Carbon|null $deleted_at
 */
class Affiliate extends AppModel
{
    use SoftDeletes;

    protected $table = 'affiliates';

    protected $fillable = [
        'affiliate_name',
        'affiliate_slug',
        'referral_link',
        'status',
        'clicks_generated',
        'earnings',
        'payout_date',
        'payout_status',
        'total_sales',
        'commission',
        'group_id',
    ];

    protected $casts = [
        'earnings'     => 'decimal:2',
        'commission'   => 'decimal:2',
        'payout_date'  => 'date',
    ];

    /**
     * Get the group this affiliate belongs to.
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
