<?php

declare(strict_types=1);

namespace App\Domains\Reviews\Enums;

enum ReviewStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Spam = 'spam';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
            self::Spam => 'Spam',
        };
    }

    public function isPubliclyVisible(): bool
    {
        return $this === self::Approved;
    }
}
