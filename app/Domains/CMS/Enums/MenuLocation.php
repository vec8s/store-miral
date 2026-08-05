<?php

declare(strict_types=1);

namespace App\Domains\CMS\Enums;

enum MenuLocation: string
{
    case Header = 'header';
    case Footer = 'footer';
    case Sidebar = 'sidebar';

    public function label(): string
    {
        return match ($this) {
            self::Header => 'Header',
            self::Footer => 'Footer',
            self::Sidebar => 'Sidebar',
        };
    }
}
