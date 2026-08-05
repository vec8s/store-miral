<?php

declare(strict_types=1);

namespace App\Domains\Sync\Enums;

enum SyncAction: string
{
    case Create = 'create';
    case Update = 'update';
    case Delete = 'delete';
    case Skip = 'skip';
    case Error = 'error';

    public function label(): string
    {
        return match ($this) {
            self::Create => 'Create',
            self::Update => 'Update',
            self::Delete => 'Delete',
            self::Skip => 'Skip',
            self::Error => 'Error',
        };
    }
}
