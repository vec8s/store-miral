<?php

declare(strict_types=1);

namespace App\Shared\Enums;

enum WeightUnit: string
{
    case Gram = 'g';
    case Kilogram = 'kg';
    case Pound = 'lb';
    case Ounce = 'oz';

    public function label(): string
    {
        return match ($this) {
            self::Gram => 'Gram',
            self::Kilogram => 'Kilogram',
            self::Pound => 'Pound',
            self::Ounce => 'Ounce',
        };
    }

    public function toGrams(int $value): int
    {
        return match ($this) {
            self::Gram => $value,
            self::Kilogram => $value * 1000,
            self::Pound => (int) round($value * 453.592),
            self::Ounce => (int) round($value * 28.3495),
        };
    }
}
