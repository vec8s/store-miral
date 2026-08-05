<?php

declare(strict_types=1);

namespace App\Shared\Enums;

enum Visibility: string
{
    case Public = 'public';
    case Private = 'private';
    case PasswordProtected = 'password';

    public function label(): string
    {
        return match ($this) {
            self::Public => 'Public',
            self::Private => 'Private',
            self::PasswordProtected => 'Password Protected',
        };
    }
}
