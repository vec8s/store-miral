<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    /**
     * تشغيل زرع بيانات المخزون المبدئية للمنتجات ومتغيراتها.
     */
    public function run(): void
    {
        // جلب جميع المتغيرات المتاحة
        $variants = ProductVariant::all();

        foreach ($variants as $variant) {
            Inventory::updateOrCreate(
                [
                    'product_id' => $variant->product_id,
                    'variant_id' => $variant->id,
                ],
                [
                    'quantity'            => 50, // كمية متوفرة
                    'reserved_quantity'   => 0,  // لا يوجد حجز مبدئي
                    'low_stock_threshold' => 5,
                    'low_stock_notified'  => false,
                    'warehouse_location'  => 'RIYADH-WH-A1',
                ]
            );
        }

        // تحضير سجل مخزون للمنتجات التي ليس لها متغيرات
        $singleProducts = Product::doesntHave('variants')->get();

        foreach ($singleProducts as $product) {
            Inventory::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'variant_id' => null,
                ],
                [
                    'quantity'            => 100,
                    'reserved_quantity'   => 0,
                    'low_stock_threshold' => 10,
                    'low_stock_notified'  => false,
                    'warehouse_location'  => 'RIYADH-WH-MAIN',
                ]
            );
        }

        $this->command?->info('✅ InventorySeeder: تم إسناد كميات المخزون والمستودعات بنجاح.');
    }
}