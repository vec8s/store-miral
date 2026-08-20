<?php

declare(strict_types=1);

namespace App\Domains\CMS\Enums;

enum MenuItemType: string
{
    case Link = 'link';
    case Category = 'category';
    case Page = 'page';
    case Product = 'product';
    case Post = 'post';
    case External = 'external';

    public function label(): string
    {
        return match ($this) {
            self::Link => 'Custom Link',
            self::Category => 'Product Category',
            self::Page => 'CMS Page',
            self::Product => 'Product',
            self::Post => 'Blog Post',
            self::External => 'External URL',
        };
    }

    public function requiresReference(): bool
    {
        return ! in_array($this, [self::Link, self::External], true);
    }
}
