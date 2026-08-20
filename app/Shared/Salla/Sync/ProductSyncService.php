<?php

declare(strict_types=1);

namespace App\Shared\Salla\Sync;

use App\Domains\Catalog\Enums\ProductOptionType;
use App\Domains\Catalog\Enums\ProductStatus;
use App\Domains\Catalog\Enums\ProductVisibility;
use App\Domains\Catalog\Enums\SyncStatus;
use App\Domains\Catalog\Models\Product;
use App\Domains\Catalog\Models\ProductImage;
use App\Domains\Catalog\Models\ProductOption;
use App\Domains\Catalog\Models\ProductOptionValue;
use App\Domains\Catalog\Models\ProductVariant;
use App\Domains\Shared\DTOs\ProductDTO;
use Illuminate\Support\Str;

/**
 * Persists Salla product payloads into the local catalog.
 *
 * The service is source-of-truth driven: every sync replaces the mutable
 * child records (images, options, variants) with the latest Salla state so
 * deletions in Salla propagate locally.
 */
final class ProductSyncService
{
    /**
     * @param  array<string, mixed>  $raw
     */
    public function syncFromSalla(array $raw): Product
    {
        $dto = ProductDTO::fromSallaResponse($raw);

        $priceMinor = self::toMinor($dto->price);
        $salePriceMinor = $dto->salePrice !== null ? self::toMinor($dto->salePrice) : null;
        $status = ProductStatus::fromSalla($dto->status ?? $this->rawStatus($raw));
        $currency = $dto->currency ?? 'SAR';

        $product = Product::updateOrCreate(
            ['salla_id' => (string) $dto->id],
            [
                'salla_product_id' => (string) $dto->id,
                'name' => $dto->name,
                'slug' => $this->uniqueSlug($dto),
                'description' => $dto->description,
                'sku' => $dto->sku,
                'mpn' => $dto->mpn,
                'barcode' => $dto->gtin,
                'status' => $status,
                'visibility' => $this->visibilityFromStatus($status),
                'is_available' => $this->isAvailable($raw, $dto),
                'price_minor' => $priceMinor,
                'sale_price_minor' => $salePriceMinor,
                'currency' => $currency,
                'quantity' => $this->stockQuantity($raw, $dto),
                'main_image_url' => $dto->thumbnail,
                'source_updated_at' => $dto->updatedAt,
                'synced_at' => now(),
                'sync_status' => SyncStatus::Synced,
                'sync_error' => null,
            ],
        );

        $this->syncImages($product, $raw['images'] ?? $dto->images);
        $this->syncOptions($product, $raw['options'] ?? []);
        $this->syncVariants($product, $raw['variants'] ?? [], $currency);

        return $product->refresh();
    }

    /**
     * @param  array<string, mixed>  $raw
     */
    public function syncDeleted(string $sallaProductId): bool
    {
        $product = Product::where('salla_id', $sallaProductId)->first();

        if ($product === null) {
            return false;
        }

        $product->delete();

        return true;
    }

    public function count(): int
    {
        return Product::count();
    }

    /**
     * @param  array<int, array<string, mixed>>  $images
     */
    private function syncImages(Product $product, array $images): void
    {
        $product->images()->delete();

        $payload = array_map(static function (array $image, int $index) use ($product): array {
            return [
                'product_id' => $product->id,
                'url' => (string) ($image['url'] ?? $image['original'] ?? ''),
                'thumbnail_url' => isset($image['thumbnail']) ? (string) $image['thumbnail'] : null,
                'medium_url' => isset($image['medium']) ? (string) $image['medium'] : null,
                'large_url' => isset($image['large']) ? (string) $image['large'] : null,
                'alt_text' => isset($image['name']) ? (string) $image['name'] : null,
                'width' => isset($image['width']) ? (int) $image['width'] : null,
                'height' => isset($image['height']) ? (int) $image['height'] : null,
                'is_main' => $index === 0,
                'sort_order' => $index,
                'synced_at' => now(),
            ];
        }, $images, array_keys($images));

        if ($payload !== []) {
            ProductImage::insert($payload);
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $options
     */
    private function syncOptions(Product $product, array $options): void
    {
        $product->options()->delete();

        foreach ($options as $index => $option) {
            $name = (string) ($option['name'] ?? $option['title'] ?? 'Option '.($index + 1));

            $model = ProductOption::create([
                'product_id' => $product->id,
                'name' => $name,
                'display_name' => isset($option['display_name']) ? (string) $option['display_name'] : null,
                'type' => $this->optionType($option),
                'sort_order' => $index,
                'is_required' => (bool) ($option['is_required'] ?? false),
            ]);

            $values = (array) ($option['values'] ?? []);

            ProductOptionValue::insert(array_map(static function (mixed $value, int $valueIndex) use ($model): array {
                $row = is_array($value) ? $value : ['value' => $value];

                return [
                    'product_option_id' => $model->id,
                    'value' => (string) ($row['value'] ?? $row['name'] ?? ''),
                    'display_value' => isset($row['display_value']) ? (string) $row['display_value'] : null,
                    'color_code' => isset($row['color_code']) ? (string) $row['color_code'] : null,
                    'image_url' => isset($row['image_url']) ? (string) $row['image_url'] : null,
                    'sort_order' => $valueIndex,
                ];
            }, $values, array_keys($values)));
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $variants
     */
    private function syncVariants(Product $product, array $variants, string $currency): void
    {
        $product->variants()->delete();

        if ($variants === []) {
            return;
        }

        ProductVariant::insert(array_map(static function (array $variant, int $index) use ($product, $currency): array {
            $price = is_array($variant['price'] ?? null) ? (float) ($variant['price']['amount'] ?? 0) : (float) ($variant['price'] ?? 0);
            $sale = $variant['sale_price'] ?? null;
            $saleMinor = is_array($sale) ? self::toMinor((float) ($sale['amount'] ?? 0)) : ($sale !== null ? self::toMinor((float) $sale) : null);

            return [
                'salla_id' => isset($variant['id']) ? (string) $variant['id'] : null,
                'product_id' => $product->id,
                'name' => (string) ($variant['name'] ?? ''),
                'sku' => isset($variant['sku']) ? (string) $variant['sku'] : null,
                'barcode' => isset($variant['barcode']) ? (string) $variant['barcode'] : null,
                'price_minor' => self::toMinor($price),
                'sale_price_minor' => $saleMinor,
                'currency' => $currency,
                'quantity' => isset($variant['quantity']) ? (int) $variant['quantity'] : 0,
                'weight' => isset($variant['weight']) ? (float) $variant['weight'] : null,
                'is_default' => $index === 0,
                'is_available' => (bool) ($variant['is_available'] ?? true),
                'synced_at' => now(),
                'sync_status' => SyncStatus::Synced,
            ];
        }, $variants, array_keys($variants)));
    }

    private function uniqueSlug(ProductDTO $dto): string
    {
        $base = $dto->name !== '' ? $dto->name : 'product-'.$dto->id;
        $slug = Str::slug($base, '-', 'ar');

        if ($slug === '') {
            $slug = 'product-'.$dto->id;
        }

        return $slug.'-'.$dto->id;
    }

    private function visibilityFromStatus(ProductStatus $status): ProductVisibility
    {
        return match ($status) {
            ProductStatus::Active => ProductVisibility::Visible,
            ProductStatus::Draft => ProductVisibility::Hidden,
            ProductStatus::Archived => ProductVisibility::Hidden,
            ProductStatus::Unknown => ProductVisibility::Visible,
        };
    }

    /** @param  array<string, mixed>  $raw */
    private function isAvailable(array $raw, ProductDTO $dto): bool
    {
        $status = ProductStatus::fromSalla($dto->status ?? $this->rawStatus($raw));

        if ($status !== ProductStatus::Active) {
            return false;
        }

        $quantity = $this->stockQuantity($raw, $dto);

        return $quantity === null || $quantity > 0;
    }

    /** @param  array<string, mixed>  $raw */
    private function stockQuantity(array $raw, ProductDTO $dto): ?int
    {
        if (isset($raw['quantity']) && is_numeric($raw['quantity'])) {
            return max(0, (int) $raw['quantity']);
        }

        return $dto->quantity;
    }

    /** @param  array<string, mixed>  $raw */
    private function rawStatus(array $raw): ?string
    {
        $status = $raw['status'] ?? null;

        return is_array($status) ? ($status['slug'] ?? null) : (is_string($status) ? $status : null);
    }

    /**
     * @param  array<string, mixed>  $option
     */
    private function optionType(array $option): ProductOptionType
    {
        $type = strtolower((string) ($option['type'] ?? $option['option_type'] ?? ''));

        return match ($type) {
            'color' => ProductOptionType::Color,
            'size' => ProductOptionType::Size,
            'text', 'input' => ProductOptionType::Text,
            'radio', 'checkbox' => ProductOptionType::Radio,
            default => ProductOptionType::Select,
        };
    }

    private static function toMinor(float $amount): int
    {
        return (int) round($amount * 100);
    }
}
