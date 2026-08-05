<?php

declare(strict_types=1);

namespace App\Shared\Enums;

enum TimeUnit: string
{
    case Second = "second";
    case Minute = "minute";
    case Hour = "hour";
    case Day = "day";
    case Week = "week";
    case Month = "month";
    case Year = "year";

    public function label(): string
    {
        return match ($this) {
            self::Second => "Second",
            self::Minute => "Minute",
            self::Hour => "Hour",
            self::Day => "Day",
            self::Week => "Week",
            self::Month => "Month",
            self::Year => "Year",
        };
    }

    public function inSeconds(): int
    {
        return match ($this) {
            self::Second => 1,
            self::Minute => 60,
            self::Hour => 3600,
            self::Day => 86400,
            self::Week => 604800,
            self::Month => 2592000,
            self::Year => 31536000,
        };
    }

    public static function fromSeconds(int $seconds): self
    {
        return match (true) {
            $seconds < 60 => self::Second,
            $seconds < 3600 => self::Minute,
            $seconds < 86400 => self::Hour,
            $seconds < 604800 => self::Day,
            $seconds < 2592000 => self::Week,
            $seconds < 31536000 => self::Month,
            default => self::Year,
        };
    }
}
