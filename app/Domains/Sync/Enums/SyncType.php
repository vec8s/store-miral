<?php

declare(strict_types=1);

namespace App\Domains\Sync\Enums;

enum SyncType: string
{
    case Full = 'full';
    case Partial = 'partial';
    case Incremental = 'incremental';
    case Webhook = 'webhook';
    case Manual = 'manual';
    case Retry = 'retry';

    public function label(): string
    {
        return match ($this) {
            self::Full => 'Full Sync',
            self::Partial => 'Partial',
            self::Incremental => 'Incremental',
            self::Webhook => 'Webhook',
            self::Manual => 'Manual',
            self::Retry => 'Retry',
        };
    }
}
