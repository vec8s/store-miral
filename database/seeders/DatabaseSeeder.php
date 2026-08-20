<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domains\Identity\Enums\RoleCode;
use App\Domains\Identity\Models\User;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);

        // Create standard default users for local testing
        if (User::count() === 0) {
            // SuperAdmin
            $admin = User::create([
                'name' => 'مدير النظام',
                'email' => 'admin@rafal.test',
                'phone' => '+966500000001',
                'password' => bcrypt('password'),
            ]);
            $admin->assignRole(RoleCode::SuperAdmin);

            // Customer
            User::create([
                'name' => 'عميل رافال',
                'email' => 'customer@rafal.test',
                'phone' => '+966500000002',
                'password' => bcrypt('password'),
            ]);
        }
    }
}
