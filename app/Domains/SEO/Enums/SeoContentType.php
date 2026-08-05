<?php

declare(strict_types=1);

namespace App\Domains\SEO\Enums;

enum SeoContentType: string
{
    case Website = "website";
    case Article = "article";
    case Product = "product";
    case Category = "category";
    case Brand = "brand";
    case Page = "page";
    case Post = "post";

    public function label(): string
    {
        return match ($this) {
            self::Website => "Website",
            self::Article => "Article",
            self::Product => "Product",
            self::Category => "Category",
            self::Brand => "Brand",
            self::Page => "Page",
            self::Post => "Post",
        };
    }
}
