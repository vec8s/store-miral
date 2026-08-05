<?php

declare(strict_types=1);

namespace App\Shared\Salla\Exceptions;

final class SallaAuthException extends SallaApiException
{
    public static function fromOAuthFailure(int $status, string $message): self
    {
        return new self(
            message: "Salla OAuth2 failure: {$message}",
            code: $status,
            response: ["reason" => "oauth"],
        );
    }
}
