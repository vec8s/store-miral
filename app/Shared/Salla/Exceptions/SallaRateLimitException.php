<?php

declare(strict_types=1);

namespace App\Shared\Salla\Exceptions;

final class SallaRateLimitException extends SallaApiException
{
    public function __construct(
        string $message,
        int $code = 429,
        private readonly int $retryAfter = 0,
        array $response = [],
    ) {
        parent::__construct($message, $code, $response);
    }

    public function retryAfter(): int
    {
        return $this->retryAfter;
    }
}
