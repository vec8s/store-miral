<?php

declare(strict_types=1);

namespace App\Domains\Identity\Enums;

enum RoleCode: string
{
    case SuperAdmin = 'super_admin';
    case Admin = 'admin';
    case Manager = 'manager';
    case Editor = 'editor';
    case Reviewer = 'reviewer';
    case Customer = 'customer';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Administrator',
            self::Admin => 'Administrator',
            self::Manager => 'Store Manager',
            self::Editor => 'Content Editor',
            self::Reviewer => 'Review Moderator',
            self::Customer => 'Customer',
        };
    }

    public function level(): int
    {
        return match ($this) {
            self::SuperAdmin => 1000,
            self::Manager => 800,
            self::Admin => 500,
            self::Editor => 300,
            self::Reviewer => 200,
            self::Customer => 100,
        };
    }

    public function isStaff(): bool
    {
        return $this !== self::Customer;
    }

    public static function default(): self
    {
        return self::Customer;
    }
}
