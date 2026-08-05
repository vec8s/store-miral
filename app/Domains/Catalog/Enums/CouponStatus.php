<?php

declare(strict_types=1);

namespace App\Domains\Catalog\Enums;

enum CouponStatus: string
{
    case Active = 'active';
    case Expired = 'expired';
    case Disabled = 'disabled';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Expired => 'Expired',
            self::Disabled => 'Disabled',
        };
    }
}
