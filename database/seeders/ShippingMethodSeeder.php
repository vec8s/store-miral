<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ShippingMethod;
use Illuminate\Database\Seeder;

class ShippingMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shippingMethods = [
            [
                'carrier_code'             => 'smsa',
                'name'                     => 'سمسا إكسبريس (SMSA)',
                'description'              => 'شحن سريع لجميع مدن المملكة',
                'price'                    => 28.00,
                'free_shipping_threshold'  => 300.00,
                'estimated_days_min'       => 1,
                'estimated_days_max'       => 2,
                'is_active'                => true,
                'sort_order'               => 1,
            ],
            [
                'carrier_code'             => 'aramex',
                'name'                     => 'أرامكس (Aramex)',
                'description'              => 'توصيل قياسي موثوق',
                'price'                    => 35.00,
                'free_shipping_threshold'  => 500.00,
                'estimated_days_min'       => 2,
                'estimated_days_max'       => 4,
                'is_active'                => true,
                'sort_order'               => 2,
            ],
            [
                'carrier_code'             => 'local_pickup',
                'name'                     => 'الاستلام من الفرع الرئيسي',
                'description'              => 'استلام مباشر من مستودع الرياض',
                'price'                    => 0.00,
                'free_shipping_threshold'  => null,
                'estimated_days_min'       => 0,
                'estimated_days_max'       => 1,
                'is_active'                => true,
                'sort_order'               => 3,
            ],
        ];

        foreach ($shippingMethods as $method) {
            ShippingMethod::updateOrCreate(
                ['carrier_code' => $method['carrier_code']],
                $method
            );
        }

        $this->command?->info('✅ ShippingMethodSeeder: تم إضافة طرق الشحن بنجاح.');
    }
}
