<?php

declare(strict_types=1);

namespace App\Shared\Health;

/**
 * Immutable result of a single health check.
 */
final class HealthReport
{
    private function __construct(
        private readonly string $name,
        private readonly bool $ok,
        private readonly string $message,
        private readonly ?string $error,
        private readonly float $durationMs,
    ) {}

    public static function ok(string $name, string $message, float $durationMs = 0.0): self
    {
        return new self($name, true, $message, null, $durationMs);
    }

    public static function failed(string $name, string $error, float $durationMs = 0.0): self
    {
        return new self($name, false, '', $error, $durationMs);
    }

    public function isOk(): bool
    {
        return $this->ok;
    }

    public function isFailed(): bool
    {
        return ! $this->ok;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function message(): string
    {
        return $this->message;
    }

    public function error(): ?string
    {
        return $this->error;
    }

    public function durationMs(): float
    {
        return $this->durationMs;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'status' => $this->ok ? 'ok' : 'failed',
            'message' => $this->message,
            'error' => $this->error,
            'duration_ms' => $this->durationMs,
        ];
    }
}