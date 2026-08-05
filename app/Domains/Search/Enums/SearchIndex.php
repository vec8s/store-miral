<?php

declare(strict_types=1);

namespace App\Domains\Search\Enums;

enum SearchIndex: string
{
    case Products = "products";
    case Posts = "posts";
    case Pages = "pages";
    case Categories = "categories";
    case Brands = "brands";

    public function label(): string
    {
        return match ($this) {
            self::Products => "Products",
            self::Posts => "Blog Posts",
            self::Pages => "Pages",
            self::Categories => "Categories",
            self::Brands => "Brands",
        };
    }

    public function modelClass(): string
    {
        return match ($this) {
            self::Products => \App\Domains\Catalog\Models\Product::class,
            self::Posts => \App\Domains\Blog\Models\Post::class,
            self::Pages => \App\Domains\CMS\Models\Page::class,
            self::Categories => \App\Domains\Catalog\Models\Category::class,
            self::Brands => \App\Domains\Catalog\Models\Brand::class,
        };
    }
}
