<?php

declare(strict_types=1);

namespace App\Domains\SEO\Enums;

enum StructuredDataType: string
{
    case Organization = 'Organization';
    case WebSite = 'WebSite';
    case Product = 'Product';
    case BreadcrumbList = 'BreadcrumbList';
    case Article = 'Article';
    case BlogPosting = 'BlogPosting';
    case LocalBusiness = 'LocalBusiness';
    case Review = 'Review';
    case AggregateRating = 'AggregateRating';
    case Offer = 'Offer';

    public function label(): string
    {
        return match ($this) {
            self::Organization => 'Organization',
            self::WebSite => 'WebSite',
            self::Product => 'Product',
            self::BreadcrumbList => 'Breadcrumb List',
            self::Article => 'Article',
            self::BlogPosting => 'Blog Posting',
            self::LocalBusiness => 'Local Business',
            self::Review => 'Review',
            self::AggregateRating => 'Aggregate Rating',
            self::Offer => 'Offer',
        };
    }
}
