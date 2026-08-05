<?php

declare(strict_types=1);

namespace App\Domains\Catalog\Enums;

enum SyncStatus: string
{
    case Pending = 'pending';
    case Syncing = 'syncing';
    case Synced = 'synced';
    case Failed = 'failed';
    case Stale = 'stale';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Syncing => 'Syncing',
            self::Synced => 'Synced',
            self::Failed => 'Failed',
            self::Stale => 'Stale',
        };
    }
}
