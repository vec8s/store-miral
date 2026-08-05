<?php

declare(strict_types=1);

namespace App\Domains\Wishlist\Enums;

enum WishlistVisibility: string
{
    case Private = 'private';
    case Public = 'public';
    case Unlisted = 'unlisted';

    public function label(): string
    {
        return match ($this) {
            self::Private => 'Private',
            self::Public => 'Public',
            self::Unlisted => 'Unlisted',
        };
    }
}
