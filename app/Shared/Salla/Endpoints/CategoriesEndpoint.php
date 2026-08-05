<?php

declare(strict_types=1);

namespace App\Shared\Salla\Endpoints;

use App\Domains\Shared\DTOs\CategoryDTO;
use App\Shared\Contracts\SallaClientContract;

final class CategoriesEndpoint
{
    public function __construct(
        private readonly SallaClientContract $client,
    ) {}

    /** @return array<int, CategoryDTO> */
    public function list(int $page = 1, int $perPage = 50): array
    {
        return $this->client->getCategories($page, $perPage);
    }

    public function find(int $categoryId): CategoryDTO
    {
        return $this->client->getCategory($categoryId);
    }

    /** @return array<int, CategoryDTO> */
    public function root(int $page = 1, int $perPage = 50): array
    {
        $response = $this->client->get("categories", [
            "parent_id" => 0,
            "page"      => $page,
            "per_page"  => $perPage,
        ]);

        return array_map(
            static fn (array $row): CategoryDTO => CategoryDTO::fromSallaResponse($row),
            $response["data"] ?? [],
        );
    }

    /** @return array<int, CategoryDTO> */
    public function children(int $parentId, int $page = 1, int $perPage = 50): array
    {
        $response = $this->client->get("categories", [
            "parent_id" => $parentId,
            "page"      => $page,
            "per_page"  => $perPage,
        ]);

        return array_map(
            static fn (array $row): CategoryDTO => CategoryDTO::fromSallaResponse($row),
            $response["data"] ?? [],
        );
    }
}
