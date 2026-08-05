<?php

declare(strict_types=1);

namespace App\Domains\Catalog\Enums;

enum ProductVisibility: string
{
    case Visible = 'visible';
    case Hidden = 'hidden';
    case Search = 'search';
    case Catalog = 'catalog';

    public function label(): string
    {
        return match ($this) {
            self::Visible => 'Visible',
            self::Hidden => 'Hidden',
            self::Search => 'Search Only',
            self::Catalog => 'Catalog Only',
        };
    }

    public static function fromSalla(?string $value): self
    {
        return match (strtolower((string) $value)) {
            'visible' => self::Visible,
            'hidden' => self::Hidden,
            'search' => self::Search,
            'catalog' => self::Catalog,
            default => self::Visible,
        };
    }
}
