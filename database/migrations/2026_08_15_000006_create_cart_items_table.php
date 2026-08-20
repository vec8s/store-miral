<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained('carts')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->cascadeOnDelete();
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->foreignId('salla_connection_id')->nullable()->constrained('salla_connections')->nullOnDelete();
            $table->string('salla_product_id')->nullable()->index();
            $table->string('salla_variant_id')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->json('options')->nullable();
            $table->json('customization')->nullable();
            $table->json('price_snapshot')->nullable();
            $table->timestamps();
            $table->index(['cart_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
