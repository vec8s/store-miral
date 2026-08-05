<?php

declare(strict_types=1);

namespace App\Domains\Catalog\Enums;

enum ProductOptionType: string
{
    case Select = 'select';
    case Radio = 'radio';
    case Color = 'color';
    case Size = 'size';
    case Text = 'text';

    public function label(): string
    {
        return match ($this) {
            self::Select => 'Select',
            self::Radio => 'Radio',
            self::Color => 'Color',
            self::Size => 'Size',
            self::Text => 'Text',
        };
    }
}
