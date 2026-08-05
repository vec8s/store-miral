<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domains\Identity\Models\Permission;
use Illuminate\Database\Seeder;

final class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['code' => 'catalog.read',   'name' => 'Catalog Read',   'group' => 'catalog'],
            ['code' => 'catalog.write',  'name' => 'Catalog Write',  'group' => 'catalog'],
            ['code' => 'cms.read',       'name' => 'CMS Read',       'group' => 'cms'],
            ['code' => 'cms.write',      'name' => 'CMS Write',      'group' => 'cms'],
            ['code' => 'blog.read',      'name' => 'Blog Read',      'group' => 'blog'],
            ['code' => 'blog.write',     'name' => 'Blog Write',     'group' => 'blog'],
            ['code' => 'orders.read',    'name' => 'Orders Read',    'group' => 'orders'],
            ['code' => 'customers.read', 'name' => 'Customers Read', 'group' => 'customers'],
            ['code' => 'reviews.read',   'name' => 'Reviews Read',   'group' => 'reviews'],
            ['code' => 'reviews.write',  'name' => 'Reviews Write',  'group' => 'reviews'],
            ['code' => 'reviews.moderate', 'name' => 'Reviews Moderate', 'group' => 'reviews'],
            ['code' => 'settings.read',  'name' => 'Settings Read',  'group' => 'settings'],
            ['code' => 'profile.read',   'name' => 'Profile Read',   'group' => 'profile'],
            ['code' => 'profile.write',  'name' => 'Profile Write',  'group' => 'profile'],
            ['code' => 'wishlist.read',  'name' => 'Wishlist Read',  'group' => 'wishlist'],
            ['code' => 'wishlist.write', 'name' => 'Wishlist Write', 'group' => 'wishlist'],
            ['code' => '*',              'name' => 'All Permissions', 'group' => 'system', 'is_protected' => true],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['code' => $permission['code']],
                $permission + [
                    'description' => null,
                    'is_protected' => $permission['is_protected'] ?? false,
                ],
            );
        }
    }
}
