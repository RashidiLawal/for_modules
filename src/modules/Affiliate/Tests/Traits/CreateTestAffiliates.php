<?php

declare(strict_types=1);

namespace Modules\Affiliate\Tests\Traits;

use BitCore\Foundation\Carbon;
use Modules\Affiliate\Models\Affiliate;

trait CreateTestAffiliates
{
    /**
     * Creates a test affiliate record.
     *
     * @param array $data Custom affiliate data to override defaults.
     * @return Affiliate
     */
    public function createAffiliate(array $data = []): Affiliate
    {
        return Affiliate::create(array_merge([
            'affiliate_name'    => 'Test Affiliate ' . uniqid(),
            'affiliate_slug'    => 'test-affiliate-' . uniqid(),
            'referral_link'     => 'https://example.com/referral/' . uniqid(),
            'status'            => 'enabled',
            'clicks_generated'  => 0,
            'earnings'          => 0.0,
            'payout_date'       => null,
            'payout_status'     => 'pending',
            'total_sales'       => 0,
            'commission'        => 5.0,
            'group_id'          => 1, // Default group ID, adjust as needed
        ], $data));
    }
}
