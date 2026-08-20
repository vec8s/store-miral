<?php

declare(strict_types=1);

namespace App\Domains\Shared\DTOs;

final readonly class OrderDTO
{
    public function __construct(
        public int $id,
        public ?int $referenceId = null,
        public ?string $placedAt = null,
        public ?string $source = null,
        public ?string $statusSlug = null,
        public ?string $statusName = null,
        public ?string $paymentMethod = null,
        public float $subTotal = 0.0,
        public float $shippingCost = 0.0,
        public float $tax = 0.0,
        public float $discounts = 0.0,
        public float $total = 0.0,
        public ?string $currency = null,
        public int $itemCount = 0,
    ) {}

    /**
     * @param  array<string, mixed>  $row
     */
    public static function fromSallaResponse(array $row): self
    {
        $amounts = is_array($row['amounts'] ?? null) ? $row['amounts'] : [];
        $status = is_array($row['status'] ?? null) ? $row['status'] : [];
        $date = is_array($row['date'] ?? null) ? $row['date'] : [];

        return new self(
            id: (int) ($row['id'] ?? 0),
            referenceId: isset($row['reference_id']) ? (int) $row['reference_id'] : null,
            placedAt: isset($date['date']) ? (string) $date['date'] : null,
            source: isset($row['source']) ? (string) $row['source'] : null,
            statusSlug: isset($status['slug']) ? (string) $status['slug'] : null,
            statusName: isset($status['name']) ? (string) $status['name'] : null,
            paymentMethod: isset($row['payment_method']) ? (string) $row['payment_method'] : null,
            subTotal: self::money($amounts['sub_total'] ?? null),
            shippingCost: self::money($amounts['shipping_cost'] ?? null),
            tax: self::money($amounts['tax'] ?? null),
            discounts: self::money($amounts['discounts'] ?? null),
            total: self::money($amounts['total'] ?? null),
            currency: isset($row['currency']) ? (string) $row['currency'] : null,
            itemCount: count((array) ($row['items'] ?? [])),
        );
    }

    /**
     * @param  array<string, mixed>|null  $money
     */
    private static function money(array|null $money): float
    {
        if (! is_array($money)) {
            return 0.0;
        }

        return (float) ($money['amount'] ?? 0);
    }
}