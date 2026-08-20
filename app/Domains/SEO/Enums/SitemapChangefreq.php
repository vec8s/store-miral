<?php

declare(strict_types=1);

namespace App\Domains\SEO\Enums;

enum SitemapChangefreq: string
{
    case Always = 'always';
    case Hourly = 'hourly';
    case Daily = 'daily';
    case Weekly = 'weekly';
    case Monthly = 'monthly';
    case Yearly = 'yearly';
    case Never = 'never';

    public function label(): string
    {
        return match ($this) {
            self::Always => 'Always',
            self::Hourly => 'Hourly',
            self::Daily => 'Daily',
            self::Weekly => 'Weekly',
            self::Monthly => 'Monthly',
            self::Yearly => 'Yearly',
            self::Never => 'Never',
        };
    }
}
