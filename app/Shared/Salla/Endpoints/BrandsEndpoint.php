<?php

declare(strict_types=1);

namespace App\Shared\Salla\Endpoints;

use App\Domains\Shared\DTOs\BrandDTO;
use App\Shared\Contracts\SallaClientContract;

final class BrandsEndpoint
{
    public function __construct(
        private readonly SallaClientContract $client,
    ) {}

    /** @return array<int, BrandDTO> */
    public function list(int $page = 1, int $perPage = 50): array
    {
        return $this->client->getBrands($page, $perPage);
    }

    public function find(int $brandId): BrandDTO
    {
        return $this->client->getBrand($brandId);
    }

    /** @return array<int, BrandDTO> */
    public function search(string $query, int $page = 1, int $perPage = 50): array
    {
        $response = $this->client->get("brands", [
            "q"        => $query,
            "page"     => $page,
            "per_page" => $perPage,
        ]);

        return array_map(
            static fn (array $row): BrandDTO => BrandDTO::fromSallaResponse($row),
            $response["data"] ?? [],
        );
    }
}
