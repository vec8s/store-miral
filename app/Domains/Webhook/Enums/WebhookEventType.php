<?php

declare(strict_types=1);

namespace App\Domains\Webhook\Enums;

use App\Domains\Sync\Enums\ResourceType;

enum WebhookEventType: string
{
    case ProductCreated = 'product.created';
    case ProductUpdated = 'product.updated';
    case ProductDeleted = 'product.deleted';
    case CategoryCreated = 'category.created';
    case CategoryUpdated = 'category.updated';
    case CategoryDeleted = 'category.deleted';
    case BrandCreated = 'brand.created';
    case BrandUpdated = 'brand.updated';
    case BrandDeleted = 'brand.deleted';
    case CouponCreated = 'coupon.created';
    case CouponUpdated = 'coupon.updated';
    case CouponDeleted = 'coupon.deleted';
    case CustomerCreated = 'customer.created';
    case CustomerUpdated = 'customer.updated';
    case OrderCreated = 'order.created';
    case OrderUpdated = 'order.updated';
    case OrderCancelled = 'order.cancelled';

    public function label(): string
    {
        return match ($this) {
            self::ProductCreated => 'Product Created',
            self::ProductUpdated => 'Product Updated',
            self::ProductDeleted => 'Product Deleted',
            self::CategoryCreated => 'Category Created',
            self::CategoryUpdated => 'Category Updated',
            self::CategoryDeleted => 'Category Deleted',
            self::BrandCreated => 'Brand Created',
            self::BrandUpdated => 'Brand Updated',
            self::BrandDeleted => 'Brand Deleted',
            self::CouponCreated => 'Coupon Created',
            self::CouponUpdated => 'Coupon Updated',
            self::CouponDeleted => 'Coupon Deleted',
            self::CustomerCreated => 'Customer Created',
            self::CustomerUpdated => 'Customer Updated',
            self::OrderCreated => 'Order Created',
            self::OrderUpdated => 'Order Updated',
            self::OrderCancelled => 'Order Cancelled',
        };
    }

    public function resourceType(): ResourceType
    {
        return match ($this) {
            self::ProductCreated, self::ProductUpdated, self::ProductDeleted => ResourceType::Product,
            self::CategoryCreated, self::CategoryUpdated, self::CategoryDeleted => ResourceType::Category,
            self::BrandCreated, self::BrandUpdated, self::BrandDeleted => ResourceType::Brand,
            self::CouponCreated, self::CouponUpdated, self::CouponDeleted => ResourceType::Coupon,
            self::CustomerCreated, self::CustomerUpdated => ResourceType::Customer,
            self::OrderCreated, self::OrderUpdated, self::OrderCancelled => ResourceType::Order,
        };
    }
}
