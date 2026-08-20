<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Commerce\Enums\SyncStatus;
use App\Domains\Commerce\Models\Customer;
use App\Shared\Enums\Gender;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        $gender = $this->faker->randomElement([Gender::Male, Gender::Female]);
        $firstName = $gender === Gender::Male
            ? $this->faker->firstNameMale()
            : $this->faker->firstNameFemale();

        return [
            'salla_id' => 'cust-'.$this->faker->unique()->uuid(),
            'salla_store_id' => null,
            'first_name' => $firstName,
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'mobile' => $this->faker->unique()->e164PhoneNumber(),
            'gender' => $gender,
            'city' => $this->faker->city(),
            'country' => 'SA',
            'addresses' => [
                [
                    'type' => 'shipping',
                    'line1' => $this->faker->streetAddress(),
                    'city' => $this->faker->city(),
                    'country' => 'SA',
                    'postal_code' => $this->faker->postcode(),
                ],
            ],
            'extra_attributes' => null,
            'source_updated_at' => now()->toIso8601String(),
            'synced_at' => now(),
            'sync_status' => SyncStatus::Synced->value,
        ];
    }
}
