<?php

declare(strict_types=1);

namespace App\Domains\Webhook\Enums;

enum WebhookEventStatus: string
{
    case Received = 'received';
    case Verified = 'verified';
    case Processing = 'processing';
    case Processed = 'processed';
    case Failed = 'failed';
    case Rejected = 'rejected';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Received => 'Received',
            self::Verified => 'Verified',
            self::Processing => 'Processing',
            self::Processed => 'Processed',
            self::Failed => 'Failed',
            self::Rejected => 'Rejected',
            self::Expired => 'Expired',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Processed, self::Failed, self::Rejected, self::Expired], true);
    }
}
