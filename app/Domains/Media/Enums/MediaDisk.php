<?php

declare(strict_types=1);

namespace App\Domains\Media\Enums;

enum MediaDisk: string
{
    case Public = 'public';
    case Private = 'private';
    case S3 = 's3';
    case R2 = 'r2';

    public function label(): string
    {
        return match ($this) {
            self::Public => 'Public',
            self::Private => 'Private',
            self::S3 => 'Amazon S3',
            self::R2 => 'Cloudflare R2',
        };
    }
}
