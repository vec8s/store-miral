<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * تشغيل عملية زرع الأقسام في قاعدة البيانات.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'الإلكترونيات',
                'slug' => 'electronics',
                'description' => 'أحدث الأجهزة الإلكترونية والهواتف والساعات الذكية.',
                'is_active' => true,
            ],
            [
                'name' => 'الملابس والأزياء',
                'slug' => 'clothing-and-fashion',
                'description' => 'تشكيلة واسعة من الملابس العصرية للرجال والنساء.',
                'is_active' => true,
            ],
            [
                'name' => 'المنزل والحديقة',
                'slug' => 'home-and-garden',
                'description' => 'أثاث منزلي، أدوات مطبخ، ومستلزمات الحديقة.',
                'is_active' => true,
            ],
        ];

       foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }

        // إخراج رسالة تأكيد في الـ Terminal
        $this->command?->info('✅ CategorySeeder: تم زرع الأقسام الرئيسية بنجاح.');
    }
}
