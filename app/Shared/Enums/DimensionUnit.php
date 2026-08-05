<?php

declare(strict_types=1);

namespace App\Shared\Enums;

enum DimensionUnit: string
{
    case Centimeter = "cm";
    case Meter = "m";
    case Inch = "in";
    case Foot = "ft";

    public function label(): string
    {
        return match ($this) {
            self::Centimeter => "Centimeter",
            self::Meter => "Meter",
            self::Inch => "Inch",
            self::Foot => "Foot",
        };
    }
}
