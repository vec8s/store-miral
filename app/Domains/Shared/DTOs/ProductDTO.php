<?php

declare(strict_types=1);

namespace App\Domains\Shared\DTOs;

final readonly class ProductDTO
{
    /**
     * @param  array<int, int>  $categoryIds
     * @param  array<int, string>  $tags
     * @param  array<int, array<string, mixed>>  $images
     */
    public function __construct(
        public int $id,
        public string $name,
        public ?string $sku = null,
        public ?string $mpn = null,
        public ?string $gtin = null,
        public ?string $type = null,
        public ?string $status = null,
        public float $price = 0.0,
        public ?string $currency = null,
        public ?float $salePrice = null,
        public ?float $costPrice = null,
        public ?int $quantity = null,
        public ?string $description = null,
        public ?string $url = null,
        public array $categoryIds = [],
        public ?int $brandId = null,
        public array $tags = [],
        public array $images = [],
        public ?string $thumbnail = null,
        public ?string $updatedAt = null,
    ) {}

    /**
     * @param  array<string, mixed>  $row
     */
    public static function fromSallaResponse(array $row): self
    {
        return new self(
            id: (int) ($row['id'] ?? 0),
            name: (string) ($row['name'] ?? ''),
            sku: isset($row['sku']) ? (string) $row['sku'] : null,
            mpn: isset($row['mpn']) ? (string) $row['mpn'] : null,
            gtin: isset($row['gtin']) ? (string) $row['gtin'] : null,
            type: isset($row['type']) ? (string) $row['type'] : null,
            status: isset($row['status']) ? (string) $row['status'] : null,
            price: self::money($row['price'] ?? null),
            currency: is_array($row['price'] ?? null) ? (isset($row['price']['currency']) ? (string) $row['price']['currency'] : null) : null,
            salePrice: self::nullableMoney($row['sale_price'] ?? null),
            costPrice: self::nullableMoney($row['cost_price'] ?? null),
            quantity: isset($row['quantity']) ? (int) $row['quantity'] : null,
            description: isset($row['description']) ? (string) $row['description'] : null,
            url: isset($row['url']) ? (string) $row['url'] : null,
            categoryIds: array_map('intval', (array) ($row['categories'] ?? [])),
            brandId: isset($row['brand_id']) ? (int) $row['brand_id'] : null,
            tags: array_map('strval', (array) ($row['tags'] ?? [])),
            images: (array) ($row['images'] ?? []),
            thumbnail: self::primaryThumbnail($row['images'] ?? []),
            updatedAt: isset($row['updated_at']) ? (string) $row['updated_at'] : null,
        );
    }

    /**
     * @param  array<string, mixed>|null  $money
     */
    private static function money(array|null $money): float
    {
        if (! is_array($money)) {
            return (float) ($money ?? 0);
        }

        return (float) ($money['amount'] ?? 0);
    }

    /**
     * @param  array<string, mixed>|null  $money
     */
    private static function nullableMoney(array|null $money): ?float
    {
        if (! is_array($money)) {
            return $money !== null ? (float) $money : null;
        }

        return isset($money['amount']) ? (float) $money['amount'] : null;
    }

    /**
     * @param  array<int, array<string, mixed>>  $images
     */
    private static function primaryThumbnail(array $images): ?string
    {
        foreach ($images as $image) {
            $url = $image['thumbnail'] ?? $image['original'] ?? null;
            if (is_string($url) && $url !== '') {
                return $url;
            }
        }

        return null;
    }
}