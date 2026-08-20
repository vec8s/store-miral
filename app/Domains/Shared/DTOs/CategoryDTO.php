<?php

declare(strict_types=1);

namespace App\Domains\Shared\DTOs;

final readonly class CategoryDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $description = null,
        public ?string $icon = null,
        public int $sort = 0,
        public int $productsCount = 0,
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
            name: (string) ($row['name'] ?? ''),
            description: isset($row['description']) ? (string) $row['description'] : null,
            icon: isset($row['icon']) ? (string) $row['icon'] : null,
            sort: (int) ($row['sort'] ?? 0),
            productsCount: (int) ($row['products_count'] ?? 0),
            status: isset($row['status']) ? (string) $row['status'] : null,
            createdAt: isset($row['created_at']) ? (string) $row['created_at'] : null,
            updatedAt: isset($row['updated_at']) ? (string) $row['updated_at'] : null,
        );
    }
}