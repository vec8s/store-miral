<?php

declare(strict_types=1);

namespace App\Shared\Salla\Endpoints;

use App\Domains\Shared\DTOs\ProductDTO;
use App\Shared\Contracts\SallaClientContract;

final class ProductsEndpoint
{
    public function __construct(
        private readonly SallaClientContract $client,
    ) {}

    /** @return array<int, ProductDTO> */
    public function list(int $page = 1, int $perPage = 50): array
    {
        return $this->client->getProducts($page, $perPage);
    }

    public function find(int $productId): ProductDTO
    {
        return $this->client->getProduct($productId);
    }

    /** @return array<int, ProductDTO> */
    public function byCategory(int $categoryId, int $page = 1, int $perPage = 50): array
    {
        $response = $this->client->get('products', [
            'category_id' => $categoryId,
            'page' => $page,
            'per_page' => $perPage,
        ]);

        return array_map(
            static fn (array $row): ProductDTO => ProductDTO::fromSallaResponse($row),
            $response['data'] ?? [],
        );
    }

    /** @return array<int, ProductDTO> */
    public function byBrand(int $brandId, int $page = 1, int $perPage = 50): array
    {
        $response = $this->client->get('products', [
            'brand_id' => $brandId,
            'page' => $page,
            'per_page' => $perPage,
        ]);

        return array_map(
            static fn (array $row): ProductDTO => ProductDTO::fromSallaResponse($row),
            $response['data'] ?? [],
        );
    }

    /** @return array<int, ProductDTO> */
    public function search(string $query, int $page = 1, int $perPage = 50): array
    {
        $response = $this->client->get('products', [
            'q' => $query,
            'page' => $page,
            'per_page' => $perPage,
        ]);

        return array_map(
            static fn (array $row): ProductDTO => ProductDTO::fromSallaResponse($row),
            $response['data'] ?? [],
        );
    }
}
