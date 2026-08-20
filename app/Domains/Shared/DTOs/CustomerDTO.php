<?php

declare(strict_types=1);

namespace App\Domains\Shared\DTOs;

final readonly class CustomerDTO
{
    public function __construct(
        public int $id,
        public string $firstName,
        public ?string $lastName = null,
        public ?string $mobile = null,
        public ?string $mobileCode = null,
        public ?string $email = null,
        public ?string $gender = null,
        public ?string $birthday = null,
        public ?string $avatar = null,
        public ?string $city = null,
        public ?string $country = null,
        public ?string $currency = null,
        public ?string $updatedAt = null,
    ) {}

    /**
     * @param  array<string, mixed>  $row
     */
    public static function fromSallaResponse(array $row): self
    {
        return new self(
            id: (int) ($row['id'] ?? 0),
            firstName: (string) ($row['first_name'] ?? ''),
            lastName: isset($row['last_name']) ? (string) $row['last_name'] : null,
            mobile: isset($row['mobile']) ? (string) $row['mobile'] : null,
            mobileCode: isset($row['mobile_code']) ? (string) $row['mobile_code'] : null,
            email: isset($row['email']) ? (string) $row['email'] : null,
            gender: isset($row['gender']) ? (string) $row['gender'] : null,
            birthday: isset($row['birthday']) ? (string) $row['birthday'] : null,
            avatar: isset($row['avatar']) ? (string) $row['avatar'] : null,
            city: isset($row['city']) ? (string) $row['city'] : null,
            country: isset($row['country']) ? (string) $row['country'] : null,
            currency: isset($row['currency']) ? (string) $row['currency'] : null,
            updatedAt: isset($row['updated_at']) ? (string) $row['updated_at'] : null,
        );
    }

    public function fullName(): string
    {
        return trim($this->firstName.' '.($this->lastName ?? ''));
    }
}