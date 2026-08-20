<?php

declare(strict_types=1);

namespace App\Domains\Shared\DTOs;

final readonly class BrandDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $logo = null,
        public ?string $cover = null,
        public ?string $url = null,
        public ?string $description = null,
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
            logo: isset($row['logo']) ? (string) $row['logo'] : null,
            cover: isset($row['cover']) ? (string) $row['cover'] : null,
            url: isset($row['url']) ? (string) $row['url'] : null,
            description: isset($row['description']) ? (string) $row['description'] : null,
            status: isset($row['status']) ? (string) $row['status'] : null,
            createdAt: isset($row['created_at']) ? (string) $row['created_at'] : null,
            updatedAt: isset($row['updated_at']) ? (string) $row['updated_at'] : null,
        );
    }
}