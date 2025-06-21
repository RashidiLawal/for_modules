<?php

declare(strict_types=1);

namespace Modules\Affiliate\Requests\Affiliates;

use BitCore\Application\Services\Requests\RequestValidator;

class CreateAffiliateRequest extends RequestValidator
{
    public static function rules(): array
    {
        return [
            'affiliate_name'   => 'required|string|max:255',
            'affiliate_slug'   => 'required|string|max:255',
            'referral_link'    => 'nullable|string|max:255|url',
            'status'           => 'required|string|in:enabled,disabled',
            'clicks_generated' => 'nullable|integer|min:0',
            'earnings'         => 'nullable|numeric|min:0',
            'payout_date'      => 'nullable|date',
            'payout_status'    => 'nullable|string|in:pending,paid,failed',
            'total_sales'      => 'nullable|integer|min:0',
            'commission'       => 'nullable|numeric|min:0',
            'group_id'         => 'nullable|integer',
        ];
    }
}
