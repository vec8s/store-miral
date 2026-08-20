<?php

declare(strict_types=1);

namespace Tests\Unit\Salla\DTOs;

use App\Domains\Shared\DTOs\CustomerDTO;
use PHPUnit\Framework\TestCase;

class CustomerDTOTest extends TestCase
{
    public function test_from_salla_response_maps_fields(): void
    {
        $dto = CustomerDTO::fromSallaResponse([
            'id' => 777,
            'first_name' => 'Ahmed',
            'last_name' => 'Ali',
            'mobile' => '555123456',
            'mobile_code' => '966',
            'email' => 'ahmed@example.com',
            'gender' => 'male',
            'birthday' => '1990-05-05',
            'avatar' => 'https://img/avatar.png',
            'city' => 'Riyadh',
            'country' => 'SA',
            'currency' => 'SAR',
            'updated_at' => '2026-08-01T00:00:00+03:00',
        ]);

        $this->assertSame(777, $dto->id);
        $this->assertSame('Ahmed', $dto->firstName);
        $this->assertSame('Ali', $dto->lastName);
        $this->assertSame('555123456', $dto->mobile);
        $this->assertSame('966', $dto->mobileCode);
        $this->assertSame('ahmed@example.com', $dto->email);
        $this->assertSame('male', $dto->gender);
        $this->assertSame('https://img/avatar.png', $dto->avatar);
        $this->assertSame('Riyadh', $dto->city);
        $this->assertSame('Ahmed Ali', $dto->fullName());
    }

    public function test_from_salla_response_handles_missing_optional_fields(): void
    {
        $dto = CustomerDTO::fromSallaResponse(['id' => 1, 'first_name' => 'Sara']);

        $this->assertSame(1, $dto->id);
        $this->assertSame('Sara', $dto->firstName);
        $this->assertNull($dto->lastName);
        $this->assertNull($dto->email);
        $this->assertNull($dto->avatar);
        $this->assertSame('Sara', $dto->fullName());
    }
}