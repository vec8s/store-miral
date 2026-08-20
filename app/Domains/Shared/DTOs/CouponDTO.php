<?php

declare(strict_types=1);

namespace App\Domains\Shared\DTOs;

final readonly class CouponDTO
{
    public function __construct(
        public int $id,
        public string $code,
        public ?string $name = null,
        public float $value = 0.0,
        public ?string $type = null,
        public ?string $startsAt = null,
        public ?string $endsAt = null,
        public float $minTotal = 0.0,
        public float $discountLimit = 0.0,
        public ?int $totalUsage = null,
        public ?int $limitPerUser = null,
        public ?string $status = null,
        public ?string $createdAt = null,
        public ?string $updatedAt = null,
    ) {}

    /**
     * @param  array<string, mixed>  $row
     */
    public static function fromSallaResponse(array $row): self
    {
        return new self(
            id: (int) ($row['id'] ?? 0),
            code: (string) ($row['code'] ?? ''),
            name: isset($row['name']) ? (string) $row['name'] : null,
            value: self::money($row['value'] ?? null),
            type: isset($row['type']) ? (string) $row['type'] : null,
            startsAt: isset($row['starts_at']) ? (string) $row['starts_at'] : null,
            endsAt: isset($row['ends_at']) ? (string) $row['ends_at'] : null,
            minTotal: self::money($row['min_total'] ?? null),
            discountLimit: self::money($row['discount_limit'] ?? null),
            totalUsage: isset($row['total_usage']) ? (int) $row['total_usage'] : null,
            limitPerUser: isset($row['limit_per_user']) ? (int) $row['limit_per_user'] : null,
            status: isset($row['status']) ? (string) $row['status'] : null,
            createdAt: isset($row['created_at']) ? (string) $row['created_at'] : null,
            updatedAt: isset($row['updated_at']) ? (string) $row['updated_at'] : null,
        );
    }

    /**
     * @param  array<string, mixed>|mixed  $money
     */
    private static function money(mixed $money): float
    {
        if (is_array($money)) {
            return (float) ($money['amount'] ?? 0);
        }

        return $money !== null ? (float) $money : 0.0;
    }
}