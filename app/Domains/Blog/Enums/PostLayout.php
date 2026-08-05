<?php

declare(strict_types=1);

namespace App\Domains\Blog\Enums;

enum PostLayout: string
{
    case Standard = 'standard';
    case Featured = 'featured';
    case Video = 'video';

    public function label(): string
    {
        return match ($this) {
            self::Standard => 'Standard',
            self::Featured => 'Featured',
            self::Video => 'Video',
        };
    }
}
