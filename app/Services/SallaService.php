<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * SallaService — Laravel PHP port of the legacy Node sallaService.ts
 *
 * Handles OAuth 2.0 Client Credentials token management, product fetching,
 * and unified product transformation. Falls back to mock data when Salla
 * credentials are absent or the API is unreachable.
 */
class SallaService
{
    private readonly string $clientId;

    private readonly string $clientSecret;

    private readonly string $baseUrl;

    private readonly string $authUrl;

    // Cache keys
    private const TOKEN_CACHE_KEY = 'salla_access_token';

    private const PRODUCT_CACHE_KEY = 'salla_products';

    private const TOKEN_TTL_BUFFER = 60;   // seconds before expiry to refresh

    public function __construct()
    {
        $this->clientId = config('services.salla.client_id', '');
        $this->clientSecret = config('services.salla.client_secret', '');
        $this->baseUrl = config('services.salla.api_url', 'https://api.salla.dev/admin/v2');
        $this->authUrl = config('services.salla.auth_url', 'https://accounts.salla.sa/oauth2/token');
    }

    // ─── Credentials ──────────────────────────────────────────────────────────

    public function hasCredentials(): bool
    {
        return $this->clientId !== '' && $this->clientSecret !== '';
    }

    // ─── Token Management ─────────────────────────────────────────────────────

    /**
     * Retrieve a valid OAuth access token, using the cache to avoid redundant
     * token requests. Returns null if credentials are missing or Salla errors.
     */
    public function getAccessToken(): ?string
    {
        if (! $this->hasCredentials()) {
            return null;
        }

        $cached = Cache::get(self::TOKEN_CACHE_KEY);
        if ($cached !== null) {
            return $cached;
        }

        try {
            $response = Http::asForm()
                ->timeout(10)
                ->retry(2, 1000)
                ->post($this->authUrl, [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                ]);

            if (! $response->successful()) {
                Log::warning('[SallaService] OAuth token request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            }

            $data = $response->json();
            $token = $data['access_token'] ?? null;
            $expiresIn = (int) ($data['expires_in'] ?? 14400);

            if ($token) {
                // Cache with a buffer so we refresh before true expiry
                Cache::put(self::TOKEN_CACHE_KEY, $token, $expiresIn - self::TOKEN_TTL_BUFFER);
            }

            return $token;
        } catch (\Throwable $e) {
            Log::error('[SallaService] Token fetch exception: '.$e->getMessage());

            return null;
        }
    }

    // ─── Product Fetching ─────────────────────────────────────────────────────

    /**
     * Fetch all products from the Salla Merchant API, caching the result for
     * 5 minutes. Falls back to mock data on failure.
     *
     * @param  array<string, int|string|null>  $params
     * @return array<int, array<string, mixed>>
     */
    public function fetchProductsFromSalla(array $params = []): array
    {
        $token = $this->getAccessToken();

        if (! $token) {
            return Cache::get(self::PRODUCT_CACHE_KEY, []);
        }

        try {
            $response = Http::withToken($token)
                ->timeout(10)
                ->retry(3, 1000)
                ->get("{$this->baseUrl}/products", array_filter([
                    'page' => $params['page'] ?? 1,
                    'per_page' => $params['per_page'] ?? 20,
                    'category_id' => $params['category_id'] ?? null,
                    'keyword' => $params['keyword'] ?? null,
                ]));

            if (! $response->successful()) {
                Log::warning('[SallaService] Products API call failed', ['status' => $response->status()]);

                return Cache::get(self::PRODUCT_CACHE_KEY, []);
            }

            $items = $response->json('data', []);
            $products = array_map([$this, 'formatSallaProduct'], $items);

            Cache::put(self::PRODUCT_CACHE_KEY, $products, 300);

            return $products;
        } catch (\Throwable $e) {
            Log::error('[SallaService] Products fetch exception: '.$e->getMessage());

            return Cache::get(self::PRODUCT_CACHE_KEY, []);
        }
    }

    /**
     * Fetch a single product by ID from Salla. Returns null on failure.
     *
     * @return array<string, mixed>|null
     */
    public function fetchProductById(int $id): ?array
    {
        $token = $this->getAccessToken();
        if (! $token) {
            return null;
        }

        try {
            $response = Http::withToken($token)
                ->timeout(10)
                ->retry(2, 1000)
                ->get("{$this->baseUrl}/products/{$id}");

            if (! $response->successful()) {
                return null;
            }

            $raw = $response->json('data');

            return $raw ? $this->formatSallaProduct($raw) : null;
        } catch (\Throwable $e) {
            Log::error("[SallaService] fetchProductById #{$id} exception: ".$e->getMessage());

            return null;
        }
    }

    // ─── Product Transformation ───────────────────────────────────────────────

    /**
     * Normalise a raw Salla API product payload into a unified array shape used
     * throughout the Blade views and controllers.
     *
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    public function formatSallaProduct(array $item): array
    {
        $rawPrice = is_array($item['price'] ?? null) ? ($item['price']['amount'] ?? 0) : ($item['price'] ?? 0);
        $rawSale = is_array($item['sale_price'] ?? null) ? ($item['sale_price']['amount'] ?? null) : ($item['sale_price'] ?? null);
        $rawRegular = is_array($item['regular_price'] ?? null) ? ($item['regular_price']['amount'] ?? 0) : ($item['regular_price'] ?? 0);

        $price = (float) ($rawRegular ?: $rawPrice ?: 100);
        $salePrice = $rawSale !== null ? (float) $rawSale : null;

        $image = $item['main_image']
            ?? $item['images'][0]['url']
            ?? 'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?auto=format&fit=crop&w=600&q=80';

        if (! str_starts_with($image, 'http')) {
            $image = 'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?auto=format&fit=crop&w=600&q=80';
        }

        return [
            'id' => (int) ($item['id'] ?? 0),
            'name' => $item['name'] ?? 'منتج سلة',
            'slug' => isset($item['sku']) ? "salla-{$item['sku']}" : "salla-product-{$item['id']}",
            'sku' => (string) ($item['sku'] ?? ''),
            'model' => (string) ($item['model'] ?? $item['sku'] ?? ''),
            'price' => $price,
            'sale_price' => $salePrice,
            'thumbnail_url' => $image,
            'images' => array_values(array_filter([
                $image,
                $item['images'][1]['url'] ?? null,
                $item['images'][2]['url'] ?? null,
                $item['images'][3]['url'] ?? null,
            ])),
            'colors' => $this->extractColors($item),
            'category' => [
                'id' => (int) ($item['category']['id'] ?? 1),
                'name' => $item['category']['name'] ?? 'عام',
            ],
            'stock' => (int) ($item['quantity'] ?? $item['stock'] ?? 10),
            'sales_count' => (int) ($item['sales_count'] ?? 0),
            'search_count' => (int) ($item['searches'] ?? $item['search_count'] ?? 0),
            'reviews_avg_rating' => (float) ($item['rating']['stars'] ?? 4.8),
            'reviews_count' => (int) ($item['rating']['count'] ?? 15),
            'description' => $item['description'] ?? $item['promotion']['title'] ?? 'منتج مستورد من منصة سلة',
            'created_at' => isset($item['created_at'])
                ? substr($item['created_at'], 0, 10)
                : now()->toDateString(),
            'source' => 'salla',
        ];
    }

    /**
     * Extract selectable colour options from a Salla product payload.
     * Salla exposes product options via "variants"; we fall back to a
     * sensible default set when no options are present.
     *
     * @param  array<string, mixed>  $item
     * @return array<int, array<string, mixed>>
     */
    protected function extractColors(array $item): array
    {
        $colors = [];

        $variants = $item['variants'] ?? $item['options'] ?? [];
        if (is_array($variants)) {
            foreach ($variants as $variant) {
                $value = $variant['name'] ?? $variant['value'] ?? null;
                if ($value !== null) {
                    $colors[] = [
                        'name' => (string) $value,
                        'hex' => (string) ($variant['color'] ?? $variant['hex'] ?? '#000000'),
                    ];
                }
            }
        }

        return $colors ?: [
            ['name' => 'ذهبي', 'hex' => '#D4AF37'],
            ['name' => 'فضي', 'hex' => '#C0C0C0'],
        ];
    }

    // ─── Higher-level Store Helpers ───────────────────────────────────────────

    /**
     * Return products, using Salla API when available and mock data as fallback.
     * Category and keyword filters are applied locally so they behave the same
     * in both the Salla and the mock fallback paths.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getProducts(?string $category = null, ?string $keyword = null): array
    {
        $products = $this->fetchProductsFromSalla(array_filter([
            'keyword' => $keyword,
        ]));

        if (empty($products)) {
            $products = $this->getMockProducts();
        }

        if ($category !== null) {
            $products = array_filter(
                $products,
                fn ($p) => ($p['category']['name'] ?? '') === $category
            );
        }

        if ($keyword !== null && $keyword !== '') {
            $needle = mb_strtolower($keyword);
            $products = array_filter(
                $products,
                fn ($p) => str_contains(mb_strtolower((string) ($p['name'] ?? '')), $needle)
                    || str_contains(mb_strtolower((string) ($p['description'] ?? '')), $needle)
                    || str_contains(mb_strtolower((string) ($p['category']['name'] ?? '')), $needle)
            );
        }

        return array_values($products);
    }

    /**
     * Return the first N featured products.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getFeaturedProducts(int $limit = 8): array
    {
        $products = $this->getProducts();

        return array_slice($products, 0, $limit);
    }

    /**
     * Products sorted by number of sales (most sold first).
     *
     * @return array<int, array<string, mixed>>
     */
    public function getBestSellers(int $limit = 8): array
    {
        $products = $this->getProducts();
        usort($products, fn ($a, $b) => ($b['sales_count'] ?? 0) <=> ($a['sales_count'] ?? 0));

        return array_slice($products, 0, $limit);
    }

    /**
     * Products sorted by average review rating (highest first).
     *
     * @return array<int, array<string, mixed>>
     */
    public function getTopRated(int $limit = 8): array
    {
        $products = $this->getProducts();
        usort($products, fn ($a, $b) => ($b['reviews_avg_rating'] ?? 0) <=> ($a['reviews_avg_rating'] ?? 0));

        return array_slice($products, 0, $limit);
    }

    /**
     * Products sorted by search popularity (most searched first).
     *
     * @return array<int, array<string, mixed>>
     */
    public function getMostSearched(int $limit = 8): array
    {
        $products = $this->getProducts();
        usort($products, fn ($a, $b) => ($b['search_count'] ?? 0) <=> ($a['search_count'] ?? 0));

        return array_slice($products, 0, $limit);
    }

    /**
     * Return a single product by ID, falling back to a mock product.
     *
     * @return array<string, mixed>
     */
    public function getProductById(int $id): array
    {
        $product = $this->fetchProductById($id);

        if ($product !== null) {
            return $product;
        }

        // Return a deterministic mock product for the given ID
        $mockProducts = $this->getMockProducts();

        return $mockProducts[($id - 1) % count($mockProducts)] ?? $mockProducts[0];
    }

    /**
     * Return related products (excluding the given product).
     *
     * @param  array<string, mixed>  $product
     * @return array<int, array<string, mixed>>
     */
    public function getRelatedProducts(array $product, int $limit = 4): array
    {
        $all = $this->getProducts();
        $related = array_filter($all, fn ($p) => $p['id'] !== $product['id']);

        return array_slice(array_values($related), 0, $limit);
    }

    /**
     * Products frequently bought together with the given product.
     *
     * @param  array<string, mixed>  $product
     * @return array<int, array<string, mixed>>
     */
    public function getBoughtTogether(array $product, int $limit = 3): array
    {
        $all = $this->getProducts();
        $sameCategory = array_values(array_filter(
            $all,
            fn ($p) => $p['id'] !== $product['id'] && ($p['category']['id'] ?? 0) === ($product['category']['id'] ?? 0)
        ));

        $others = array_values(array_filter($all, fn ($p) => $p['id'] !== $product['id']));

        $together = array_merge($sameCategory, $others);

        return array_slice($together, 0, $limit);
    }

    /**
     * Legal and contact information for the store footer and product page.
     *
     * @return array<string, string>
     */
    public function getStoreInfo(): array
    {
        return [
            'name' => 'ميرال',
            'description' => 'متجر حلي وهدايا فاخرة — تشكيلات عصرية بمعايير استثنائية مرتبطة بمنصة سلة.',
            'commercial_registration' => '1010123456',
            'tax_number' => '310123456700003',
            'email' => 'support@miral.sa',
            'phone' => '+966 55 000 0000',
            'address' => 'الرياض — المملكة العربية السعودية',
        ];
    }

    /**
     * Return available categories derived from products.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getCategories(): array
    {
        $products = $this->getProducts();
        $seen = [];
        $categories = [];

        foreach ($products as $product) {
            $cat = $product['category'] ?? [];
            $catId = $cat['id'] ?? 0;

            if (! isset($seen[$catId])) {
                $seen[$catId] = true;
                $categories[] = $cat;
            }
        }

        return $categories ?: $this->getMockCategories();
    }

    // ─── Mock Data ────────────────────────────────────────────────────────────

    /**
     * Deterministic mock product set used when Salla API is unavailable.
     * Keeps the UI functional during development and when credentials are missing.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getMockProducts(): array
    {
        $names = ['سلسلة ذهبية', 'ساعة فاخرة', 'بوكس هدايا', 'سبحة عقيق', 'ميدالية فضية', 'قلم راقي', 'سوار لؤلؤ', 'لوحة إسلامية'];
        $cats = ['السلاسل', 'الساعات', 'الهدايا', 'العقيق', 'الميداليات', 'الأقلام', 'الإكسسوارات', 'الديكور'];
        $prices = [349, 520, 199, 449, 275, 180, 390, 650];
        $sales = [320, 210, 180, 420, 95, 60, 260, 55];
        $searches = [150, 120, 90, 85, 75, 60, 55, 40];

        return array_map(function (int $i) use ($names, $cats, $prices, $sales, $searches) {
            return [
                'id' => $i + 1,
                'name' => $names[$i % count($names)].' '.($i + 1),
                'slug' => 'product-'.($i + 1),
                'sku' => 'MIR-'.str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT),
                'model' => 'MIR-'.str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT),
                'price' => $prices[$i % count($prices)] + ($i * 10),
                'sale_price' => $i % 3 === 0 ? $prices[$i % count($prices)] - 50 : null,
                'thumbnail_url' => "https://picsum.photos/seed/miral{$i}/400/400",
                'images' => [
                    "https://picsum.photos/seed/miral{$i}/400/400",
                    "https://picsum.photos/seed/miral{$i}b/400/400",
                    "https://picsum.photos/seed/miral{$i}c/400/400",
                ],
                'colors' => [
                    ['name' => 'ذهبي', 'hex' => '#D4AF37'],
                    ['name' => 'فضي', 'hex' => '#C0C0C0'],
                    ['name' => 'أسود', 'hex' => '#1a1a1a'],
                ],
                'category' => ['id' => ($i % 8) + 1, 'name' => $cats[$i % count($cats)]],
                'stock' => 10 + ($i * 3),
                'sales_count' => $sales[$i % count($sales)],
                'search_count' => $searches[$i % count($searches)],
                'reviews_avg_rating' => 4.5 + round(($i % 5) * 0.1, 1),
                'reviews_count' => 10 + ($i * 4),
                'description' => 'منتج فاخر مستورد من منصة سلة Salla',
                'created_at' => now()->subDays($i)->toDateString(),
                'source' => 'mock',
            ];
        }, range(0, 11));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getMockCategories(): array
    {
        return [
            ['id' => 1, 'name' => 'السلاسل',       'icon' => '⛓️',  'count' => 12],
            ['id' => 2, 'name' => 'الساعات',       'icon' => '⌚',  'count' => 8],
            ['id' => 3, 'name' => 'الهدايا',       'icon' => '🎁',  'count' => 15],
            ['id' => 4, 'name' => 'العقيق',        'icon' => '💎',  'count' => 10],
            ['id' => 5, 'name' => 'الميداليات',    'icon' => '🏅',  'count' => 6],
            ['id' => 6, 'name' => 'الأقلام',       'icon' => '🖊️', 'count' => 9],
            ['id' => 7, 'name' => 'الإكسسوارات',  'icon' => '💍',  'count' => 14],
            ['id' => 8, 'name' => 'الديكور',       'icon' => '🕌',  'count' => 7],
        ];
    }

    /**
     * Hero banner slides for the storefront homepage.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getMockBanners(): array
    {
        return [
            [
                'id' => 1,
                'badge' => 'تشكيلة موسم 2026 الحصرية',
                'title' => 'أناقة صُممت لتدوم، وتُهدى بكل حب',
                'subtitle' => 'اكتشف تشكيلة ميرال الفاخرة من الحلي والهدايا المصممة بعناية فائقة. تجربة تسوق سلسة مرتبطة ومحدثة فورياً عبر منصة سلة.',
                'image' => '💎',
                'label' => 'تصفّح التشكيلة الآن',
                'url' => '/shop',
            ],
            [
                'id' => 2,
                'badge' => 'عرض الأسبوع',
                'title' => 'تخفيضات تصل إلى 40% على الحلي المميزة',
                'subtitle' => 'خصومات حصرية لفترة محدودة على مختارات من السلاسل والميداليات والساعات. لا تفوّت الفرصة.',
                'image' => '✨',
                'label' => 'تسوّق العروض',
                'url' => '/shop?q=عرض',
            ],
            [
                'id' => 3,
                'badge' => 'شحن مجاني',
                'title' => 'توصيل مجاني للطلبات فوق 300 ر.س',
                'subtitle' => 'استمتع بشحن فوري لجميع مدن المملكة، مع تغليف هدايا فاخر مجاني مع كل طلب.',
                'image' => '🚚',
                'label' => 'اكتشف المزيد',
                'url' => '/shipping',
            ],
        ];
    }

    /**
     * Blog-style posts shown in the "منشورات يتم استعراضها باستمرار" section.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getMockPosts(): array
    {
        return [
            [
                'id' => 1,
                'title' => 'دليل اختيار الهدية المثالية في 2026',
                'excerpt' => 'كيف تختار هدية تناسب ذوق من تحب؟ نصائح عملية من خبراء ميرال.',
                'tag' => 'أدلة',
                'read_time' => '5 دقائق',
                'created_at' => now()->subDays(3)->toDateString(),
                'url' => '/faq',
            ],
            [
                'id' => 2,
                'title' => 'العناية بالحلي الذهبية: 7 خطوات للحفاظ على اللمعان',
                'excerpt' => 'طرق بسيطة وفعالة تحافظ على بريق حليك المفضلة لأطول فترة ممكنة.',
                'tag' => 'عناية',
                'read_time' => '4 دقائق',
                'created_at' => now()->subDays(7)->toDateString(),
                'url' => '/faq',
            ],
            [
                'id' => 3,
                'title' => 'أفكار إهداء مبتكرة لمناسبات العيد والزواج',
                'excerpt' => 'تشكيلة مختارة من باقات الإهداء تناسب أجمل المناسبات مع تغليف فاخر.',
                'tag' => 'إلهام',
                'read_time' => '6 دقائق',
                'created_at' => now()->subDays(12)->toDateString(),
                'url' => '/faq',
            ],
        ];
    }

    /**
     * Promotional coupon codes with their conditions.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getMockCoupons(): array
    {
        return [
            [
                'id' => 1,
                'code' => 'MIRAL15',
                'label' => 'خصم 15% على المنتجات ذات السعر العالي',
                'condition' => 'للمنتجات التي تزيد عن 500 ر.س',
                'type' => 'high_price',
            ],
            [
                'id' => 2,
                'code' => 'GIFT2',
                'label' => 'خصم 10% عند شراء قطعتين',
                'condition' => 'عند إضافة قطعتين أو أكثر إلى السلة',
                'type' => 'quantity',
            ],
            [
                'id' => 3,
                'code' => 'WELCOME',
                'label' => 'خصم 5% على أول طلب',
                'condition' => 'للعملاء الجدد — ساري على أي طلب',
                'type' => 'first_order',
            ],
        ];
    }

    // ─── Sync Status ──────────────────────────────────────────────────────────

    /**
     * Return current Salla integration status for the admin dashboard.
     *
     * @return array<string, mixed>
     */
    public function getSyncStatus(): array
    {
        $hasCreds = $this->hasCredentials();
        $token = $hasCreds ? $this->getAccessToken() : null;

        $maskedClientId = $hasCreds
            ? substr($this->clientId, 0, 4).'...'.substr($this->clientId, -3)
            : null;

        $cachedProducts = Cache::get(self::PRODUCT_CACHE_KEY, []);

        return [
            'connected' => $token !== null,
            'hasCredentials' => $hasCreds,
            'lastSyncAt' => Cache::get('salla_last_sync_at'),
            'sallaProductsCount' => count($cachedProducts),
            'message' => $token
                ? 'تم الاتصال بنجاح بـ Salla Merchant API'
                : ($hasCreds ? 'فشل التحقق من مفاتيح Salla' : 'مفاتيح SALLA_CLIENT_ID غير متوفرة'),
            'clientId' => $maskedClientId,
        ];
    }
}
