<?php

declare(strict_types=1);

namespace App\Domains\Sync\Enums;

enum ResourceType: string
{
    case Product = 'product';
    case Category = 'category';
    case Brand = 'brand';
    case Coupon = 'coupon';
    case Customer = 'customer';
    case Order = 'order';

    public function label(): string
    {
        return match ($this) {
            self::Product => 'Product',
            self::Category => 'Category',
            self::Brand => 'Brand',
            self::Coupon => 'Coupon',
            self::Customer => 'Customer',
            self::Order => 'Order',
        };
    }
}
