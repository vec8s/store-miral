<?php

return [
    'store_name' => env('STORE_NAME', 'ميرال — متجر الحلي والهدايا الفاخرة'),
    'store_phone' => env('STORE_PHONE', '+966 50 000 0000'),
    'store_email' => env('STORE_EMAIL', 'support@miral.sa'),
    'shipping_fee' => (int) env('SHIPPING_FEE', 25),
    'free_shipping_min' => (int) env('FREE_SHIPPING_MIN', 300),
    'cod_fee' => (int) env('COD_FEE', 10),
];
