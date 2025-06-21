<?php

declare(strict_types=1);

namespace Modules\Affiliate\Requests\Affiliates;

use BitCore\Application\Services\Requests\RequestValidator;

class UpdateAffiliateRequest extends RequestValidator
{
    /**
     * Define validation rules for updating an affiliate.
     *
     * @return array
     */
    public static function rules(): array
    {
        return [
            'affiliate_name'   => 'required|string|max:255',
            'affiliate_slug'   => 'required|string|max:255|alpha_dash',
            'referral_link'    => 'nullable|url|max:255',
            'status'           => 'required|in:enabled,disabled',
            'clicks_generated' => 'nullable|integer|min:0',
            'earnings'         => 'nullable|numeric|min:0',
            'payout_date'      => 'nullable|date',
            'payout_status'    => 'nullable|in:paid,pending,failed',
            'total_sales'      => 'nullable|integer|min:0',
            'commission'       => 'nullable|numeric|min:0',
            'group_id'         => 'nullable|integer',
        ];
    }
}
