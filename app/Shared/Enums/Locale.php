<?php

declare(strict_types=1);

namespace App\Shared\Enums;

enum Locale: string
{
    case Arabic = 'ar';
    case English = 'en';

    public function label(): string
    {
        return match ($this) {
            self::Arabic => 'العربية',
            self::English => 'English',
        };
    }

    public function direction(): string
    {
        return match ($this) {
            self::Arabic => 'rtl',
            self::English => 'ltr',
        };
    }
}
