<?php

declare(strict_types=1);

namespace App\Shared\Salla\Exceptions;

use Exception;
use Throwable;

class SallaApiException extends Exception
{
    /**
     * @param  array<string, mixed>  $response
     */
    public function __construct(
        string $message,
        int $code = 0,
        private readonly array $response = [],
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return array<string, mixed>
     */
    public function response(): array
    {
        return $this->response;
    }
}
