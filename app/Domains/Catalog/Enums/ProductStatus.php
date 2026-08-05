<?php

declare(strict_types=1);

namespace App\Domains\Catalog\Enums;

enum ProductStatus: string
{
    case Active = 'active';
    case Draft = 'draft';
    case Archived = 'archived';
    case Unknown = 'unknown';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Draft => 'Draft',
            self::Archived => 'Archived',
            self::Unknown => 'Unknown',
        };
    }

    public static function fromSalla(?string $value): self
    {
        return match (strtolower((string) $value)) {
            'active', 'published' => self::Active,
            'draft' => self::Draft,
            'archived' => self::Archived,
            default => self::Unknown,
        };
    }
}
