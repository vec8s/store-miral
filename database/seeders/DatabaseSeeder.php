<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Foundation
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            SettingSeeder::class,

            // Salla
            SallaTokenSeeder::class,

            // Catalog (synced from Salla)
            CatalogSampleSeeder::class,
            CouponSampleSeeder::class,

            // Storefront features
            MenuSeeder::class,
            PageSeeder::class,
            RedirectSeeder::class,

            // Blog
            PostCategorySeeder::class,
            PostTagSeeder::class,
            PostSeeder::class,

            // User-generated content
            WishlistSampleSeeder::class,
            ReviewSampleSeeder::class,

            // Operations
            WebhookEndpointSeeder::class,
        ]);
    }
}
