<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('salla_id')->unique();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->longText('description')->nullable();
            $table->string('sku')->nullable()->index();
            $table->string('mpn')->nullable();
            $table->string('barcode')->nullable()->index();
            $table->enum('status', ['active', 'draft', 'archived', 'unknown'])->default('unknown')->index();
            $table->enum('visibility', ['visible', 'hidden', 'search', 'catalog'])->default('visible')->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_on_sale')->default(false)->index();
            $table->boolean('is_free_shipping')->default(false);
            $table->boolean('requires_shipping')->default(true);
            $table->boolean('is_taxable')->default(true);
            $table->unsignedBigInteger('price_minor')->default(0);
            $table->unsignedBigInteger('sale_price_minor')->nullable();
            $table->string('currency', 3)->default('SAR');
            $table->unsignedInteger('quantity')->default(0)->index();
            $table->unsignedInteger('low_stock_threshold')->nullable();
            $table->decimal('weight', 10, 3)->nullable();
            $table->string('weight_unit', 16)->default('kg');
            $table->json('dimensions')->nullable();
            $table->string('main_image_url')->nullable();
            $table->unsignedInteger('view_count')->default(0);
            $table->unsignedInteger('sold_count')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->unsignedInteger('reviews_count')->default(0);
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('seo_keywords')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('source_updated_at')->nullable()->index();
            $table->timestamp('synced_at')->nullable()->index();
            $table->enum('sync_status', ['pending', 'syncing', 'synced', 'failed', 'stale'])->default('pending')->index();
            $table->text('sync_error')->nullable();
            $table->json('extra_attributes')->nullable();
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['status', 'visibility']);
            $table->index(['category_id', 'status', 'visibility']);
            $table->index(['brand_id', 'status']);
            $table->index(['is_featured', 'status', 'visibility']);
            $table->index(['sync_status', 'synced_at']);
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
