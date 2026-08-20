<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->foreignId('salla_connection_id')->nullable()->constrained('salla_connections')->nullOnDelete();
            $table->string('salla_id')->nullable();
            $table->string('salla_product_id')->nullable()->index();
            $table->string('salla_variant_id')->nullable();
            $table->string('name');
            $table->string('sku')->nullable()->index();
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedBigInteger('unit_price_minor')->default(0);
            $table->unsignedBigInteger('total_minor')->default(0);
            $table->json('options')->nullable();
            $table->json('customization')->nullable();
            $table->timestamps();
            $table->index(['order_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
