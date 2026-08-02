<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductAndCategorySeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::firstOrCreate(

            ['slug' => 'electronics'],
            ['name' => 'الإلكترونيات', 'is_active' => true]
        );

        $product = Product::firstOrCreate(
            ['sku' => 'PROD-SMARTWATCH-01'],
            [
                'category_id' => $category->id,
                'name' => 'ساعة ذكية متطورة',
                'slug' => Str::slug('ساعة ذكية متطورة'),
                'description' => 'ساعة ذكية تدعم تتبع اللياقة البدنية والاتصال.',
                'price' => 450.00,
                'sale_price' => 399.00,
                'is_active' => true,
                'stock_status' => 'in_stock',
            ]
        );

        // إضافة متغيرات للمنتج (مثل الألوان أو المقاسات)
        ProductVariant::firstOrCreate(
            ['sku' => 'VAR-WATCH-BLACK'],
            [
                'product_id' => $product->id,
                'label' => 'أسود',
                'price' => 399.00,
                'is_active' => true,
            ]
        );
    }
}
