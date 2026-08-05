<?php

declare(strict_types=1);

namespace App\Domains\Webhook\Enums;

enum SignatureAlgorithm: string
{
    case Sha256 = 'sha256';
    case Sha512 = 'sha512';
    case HmacSha256 = 'hmac_sha256';

    public function label(): string
    {
        return match ($this) {
            self::Sha256 => 'SHA-256',
            self::Sha512 => 'SHA-512',
            self::HmacSha256 => 'HMAC SHA-256',
        };
    }
}
