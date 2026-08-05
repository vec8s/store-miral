<?php

declare(strict_types=1);

namespace App\Domains\CMS\Enums;

enum PageTemplate: string
{
    case Default = 'default';
    case Landing = 'landing';
    case FullWidth = 'full_width';
    case Contact = 'contact';

    public function label(): string
    {
        return match ($this) {
            self::Default => 'Default',
            self::Landing => 'Landing Page',
            self::FullWidth => 'Full Width',
            self::Contact => 'Contact',
        };
    }
}
