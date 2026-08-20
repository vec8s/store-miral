<?php

declare(strict_types=1);

namespace App\Shared\Salla\Endpoints;

use App\Domains\Shared\DTOs\CustomerDTO;
use App\Shared\Contracts\SallaClientContract;

final class CustomersEndpoint
{
    public function __construct(
        private readonly SallaClientContract $client,
    ) {}

    /** @return array<int, CustomerDTO> */
    public function list(int $page = 1, int $perPage = 50): array
    {
        return $this->client->getCustomers($page, $perPage);
    }

    public function find(int $customerId): CustomerDTO
    {
        return $this->client->getCustomer($customerId);
    }

    /** @return array<int, CustomerDTO> */
    public function search(string $query, int $page = 1, int $perPage = 50): array
    {
        $response = $this->client->get('customers', [
            'q' => $query,
            'page' => $page,
            'per_page' => $perPage,
        ]);

        return array_map(
            static fn (array $row): CustomerDTO => CustomerDTO::fromSallaResponse($row),
            $response['data'] ?? [],
        );
    }
}
