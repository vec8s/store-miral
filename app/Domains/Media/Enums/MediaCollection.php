<?php

declare(strict_types=1);

namespace App\Domains\Media\Enums;

enum MediaCollection: string
{
    case Default = 'default';
    case Avatar = 'avatar';
    case Cover = 'cover';
    case Gallery = 'gallery';
    case Featured = 'featured';

    public function label(): string
    {
        return match ($this) {
            self::Default => 'Default',
            self::Avatar => 'Avatar',
            self::Cover => 'Cover',
            self::Gallery => 'Gallery',
            self::Featured => 'Featured',
        };
    }
}
