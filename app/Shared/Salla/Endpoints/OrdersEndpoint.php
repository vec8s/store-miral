<?php

declare(strict_types=1);

namespace App\Shared\Salla\Endpoints;

use App\Domains\Shared\DTOs\OrderDTO;
use App\Shared\Contracts\SallaClientContract;

final class OrdersEndpoint
{
    public function __construct(
        private readonly SallaClientContract $client,
    ) {}

    /** @return array<int, OrderDTO> */
    public function list(int $page = 1, int $perPage = 50): array
    {
        return $this->client->getOrders($page, $perPage);
    }

    public function find(int $orderId): OrderDTO
    {
        return $this->client->getOrder($orderId);
    }

    /** @return array<int, OrderDTO> */
    public function byCustomer(int $customerId, int $page = 1, int $perPage = 50): array
    {
        $response = $this->client->get("orders", [
            "customer_id" => $customerId,
            "page"        => $page,
            "per_page"    => $perPage,
        ]);

        return array_map(
            static fn (array $row): OrderDTO => OrderDTO::fromSallaResponse($row),
            $response["data"] ?? [],
        );
    }

    /** @return array<int, OrderDTO> */
    public function byStatus(string $status, int $page = 1, int $perPage = 50): array
    {
        $response = $this->client->get("orders", [
            "status"   => $status,
            "page"     => $page,
            "per_page" => $perPage,
        ]);

        return array_map(
            static fn (array $row): OrderDTO => OrderDTO::fromSallaResponse($row),
            $response["data"] ?? [],
        );
    }

    /** @return array<int, OrderDTO> */
    public function byDateRange(string $fromIso, string $toIso, int $page = 1, int $perPage = 50): array
    {
        $response = $this->client->get("orders", [
            "from"     => $fromIso,
            "to"       => $toIso,
            "page"     => $page,
            "per_page" => $perPage,
        ]);

        return array_map(
            static fn (array $row): OrderDTO => OrderDTO::fromSallaResponse($row),
            $response["data"] ?? [],
        );
    }
}
