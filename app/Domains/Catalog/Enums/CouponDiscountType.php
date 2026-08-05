<?php

declare(strict_types=1);

namespace App\Domains\Catalog\Enums;

enum CouponDiscountType: string
{
    case Fixed = 'fixed';
    case Percentage = 'percentage';

    public function label(): string
    {
        return match ($this) {
            self::Fixed => 'Fixed',
            self::Percentage => 'Percentage',
        };
    }
}
