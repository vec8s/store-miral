<?php

declare(strict_types=1);

namespace App\Http\Controllers\Storefront\Concerns;

trait NormalizesGift
{
    /**
     * Ensure every cart item carries the full gift structure (defaults).
     *
     * @param  array<int, array<string, mixed>>  $cart
     * @return array<int, array<string, mixed>>
     */
    protected function normalizeGift(array $cart): array
    {
        foreach ($cart as $key => $item) {
            $cart[$key]['color'] = (string) ($item['color'] ?? '');
            $cart[$key]['gift'] = array_merge([
                'enabled' => false,
                'recipient_name' => '',
                'recipient_phone' => '',
                'message' => '',
                'hide_price' => false,
            ], $item['gift'] ?? []);
        }

        return $cart;
    }
}
