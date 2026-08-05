<?php

declare(strict_types=1);

namespace App\Domains\Sync\Enums;

enum SyncTrigger: string
{
    case Scheduled = 'scheduled';
    case Webhook = 'webhook';
    case Manual = 'manual';
    case Retry = 'retry';
    case Api = 'api';

    public function label(): string
    {
        return match ($this) {
            self::Scheduled => 'Scheduled',
            self::Webhook => 'Webhook',
            self::Manual => 'Manual',
            self::Retry => 'Retry',
            self::Api => 'API',
        };
    }
}
