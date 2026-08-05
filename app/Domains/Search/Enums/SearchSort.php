<?php

declare(strict_types=1);

namespace App\Domains\Search\Enums;

enum SearchSort: string
{
    case Relevance = "relevance";
    case Newest = "newest";
    case Oldest = "oldest";
    case PriceAsc = "price_asc";
    case PriceDesc = "price_desc";
    case NameAsc = "name_asc";
    case NameDesc = "name_desc";
    case BestSelling = "best_selling";
    case TopRated = "top_rated";

    public function label(): string
    {
        return match ($this) {
            self::Relevance => "Relevance",
            self::Newest => "Newest First",
            self::Oldest => "Oldest First",
            self::PriceAsc => "Price: Low to High",
            self::PriceDesc => "Price: High to Low",
            self::NameAsc => "Name: A to Z",
            self::NameDesc => "Name: Z to A",
            self::BestSelling => "Best Selling",
            self::TopRated => "Top Rated",
        };
    }
}
