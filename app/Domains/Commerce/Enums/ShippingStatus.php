<?php

declare(strict_types=1);

namespace App\Domains\Commerce\Enums;

enum ShippingStatus: string
{
    case NotShipped = 'not_shipped';
    case Preparing = 'preparing';
    case Shipped = 'shipped';
    case InTransit = 'in_transit';
    case Delivered = 'delivered';
    case Returned = 'returned';

    public function label(): string
    {
        return match ($this) {
            self::NotShipped => 'Not Shipped',
            self::Preparing => 'Preparing',
            self::Shipped => 'Shipped',
            self::InTransit => 'In Transit',
            self::Delivered => 'Delivered',
            self::Returned => 'Returned',
        };
    }
}
