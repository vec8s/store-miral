<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Identity\Models\User;
use App\Domains\Settings\Models\ApiLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ApiLog>
 */
class ApiLogFactory extends Factory
{
    protected $model = ApiLog::class;

    public function definition(): array
    {
        $statusCode = $this->faker->randomElement([200, 200, 200, 201, 400, 401, 500]);
        $isError = $statusCode >= 400;

        return [
            "service" => "salla",
            "method" => $this->faker->randomElement(["GET", "POST", "PUT", "DELETE"]),
            "endpoint" => "/admin/v2/" . $this->faker->slug(2),
            "status_code" => $statusCode,
            "request_headers" => ["Authorization" => "[REDACTED]"],
            "request_body" => ["key" => "value"],
            "response_headers" => ["Content-Type" => "application/json"],
            "response_body" => "{\"success\":true}",
            "duration_ms" => $this->faker->numberBetween(50, 2000),
            "request_id" => $this->faker->uuid(),
            "correlation_id" => $this->faker->uuid(),
            "source_ip" => $this->faker->ipv4(),
            "user_id" => User::factory(),
            "is_error" => $isError,
            "error_message" => $isError ? $this->faker->sentence() : null,
            "occurred_at" => now(),
        ];
    }

    public function successful(): static
    {
        return $this->state(fn () => [
            "status_code" => 200,
            "is_error" => false,
            "error_message" => null,
        ]);
    }

    public function error(int $statusCode = 500): static
    {
        return $this->state(fn () => [
            "status_code" => $statusCode,
            "is_error" => true,
            "error_message" => $this->faker->sentence(),
        ]);
    }
}
