<?php

declare(strict_types=1);

namespace App\Shared\Enums;

enum Environment: string
{
    case Local = "local";
    case Testing = "testing";
    case Staging = "staging";
    case Production = "production";

    public function label(): string
    {
        return match ($this) {
            self::Local => "Local",
            self::Testing => "Testing",
            self::Staging => "Staging",
            self::Production => "Production",
        };
    }

    public function isDebuggable(): bool
    {
        return in_array($this, [self::Local, self::Testing, self::Staging], true);
    }

    public function isProduction(): bool
    {
        return $this === self::Production;
    }

    public static function current(): self
    {
        return self::from(app()->environment());
    }
}
