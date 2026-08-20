<?php

declare(strict_types=1);

namespace App\Domains\CMS\Casts;

use App\Domains\Blog\Models\Post;
use App\Domains\Blog\Models\PostCategory;
use App\Domains\Catalog\Models\Brand;
use App\Domains\Catalog\Models\Category;
use App\Domains\Catalog\Models\Product;
use App\Domains\CMS\Enums\MenuItemType;
use App\Domains\CMS\Models\Page;
use App\Shared\Exceptions\InvalidPayloadException;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

final class MenuItemReferenceCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?int
    {
        return $value === null ? null : (int) $value;
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): array
    {
        if ($value === null) {
            if ($this->typeRequiresReference($attributes)) {
                throw InvalidPayloadException::forField(
                    'reference_id',
                    'A reference is required for this menu item type.',
                );
            }

            return [$key => null];
        }

        $type = $this->resolveType($attributes);
        $referenceId = (int) $value;

        if ($type->requiresReference() && $referenceId <= 0) {
            throw InvalidPayloadException::forField(
                'reference_id',
                'Reference ID must be a positive integer.',
            );
        }

        if ($type->requiresReference()) {
            $this->assertReferenceExists($type, $referenceId);
        }

        return [$key => $referenceId];
    }

    private function resolveType(array $attributes): MenuItemType
    {
        $raw = $attributes['type'] ?? null;

        if ($raw === null) {
            throw InvalidPayloadException::forField('type', 'Menu item type is required.');
        }

        $type = $raw instanceof MenuItemType
            ? $raw
            : MenuItemType::tryFrom((string) $raw);

        if ($type === null) {
            throw InvalidPayloadException::forField(
                'type',
                sprintf('Invalid menu item type [%s].', (string) $raw),
            );
        }

        return $type;
    }

    private function typeRequiresReference(array $attributes): bool
    {
        try {
            $type = $this->resolveType($attributes);

            return $type->requiresReference();
        } catch (InvalidPayloadException) {
            return false;
        }
    }

    private function assertReferenceExists(MenuItemType $type, int $referenceId): void
    {
        $exists = match ($type) {
            MenuItemType::Category => Category::whereKey($referenceId)->exists(),
            MenuItemType::Page => Page::whereKey($referenceId)->exists(),
            MenuItemType::Product => Product::whereKey($referenceId)->exists(),
            MenuItemType::Post => Post::whereKey($referenceId)->exists(),
            MenuItemType::PostCategory => PostCategory::whereKey($referenceId)->exists(),
            MenuItemType::Brand => Brand::whereKey($referenceId)->exists(),
            MenuItemType::Link, MenuItemType::External => true,
        };

        if (! $exists) {
            throw InvalidPayloadException::forField(
                'reference_id',
                sprintf('Referenced %s [%d] does not exist.', $type->value, $referenceId),
            );
        }
    }
}
