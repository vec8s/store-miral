<?php

declare(strict_types=1);

namespace App\Domains\Search\Enums;

use App\Domains\Blog\Models\Post;
use App\Domains\Catalog\Models\Brand;
use App\Domains\Catalog\Models\Category;
use App\Domains\Catalog\Models\Product;
use App\Domains\CMS\Models\Page;

enum SearchIndex: string
{
    case Products = 'products';
    case Posts = 'posts';
    case Pages = 'pages';
    case Categories = 'categories';
    case Brands = 'brands';

    public function label(): string
    {
        return match ($this) {
            self::Products => 'Products',
            self::Posts => 'Blog Posts',
            self::Pages => 'Pages',
            self::Categories => 'Categories',
            self::Brands => 'Brands',
        };
    }

    public function modelClass(): string
    {
        return match ($this) {
            self::Products => Product::class,
            self::Posts => Post::class,
            self::Pages => Page::class,
            self::Categories => Category::class,
            self::Brands => Brand::class,
        };
    }
}
