<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->string('salla_id')->unique();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('sku')->nullable()->index();
            $table->string('barcode')->nullable();
            $table->unsignedBigInteger('price_minor')->default(0);
            $table->unsignedBigInteger('sale_price_minor')->nullable();
            $table->string('currency', 3)->default('SAR');
            $table->unsignedInteger('quantity')->default(0)->index();
            $table->decimal('weight', 10, 3)->nullable();
            $table->boolean('is_default')->default(false)->index();
            $table->boolean('is_available')->default(true)->index();
            $table->string('source_updated_at')->nullable()->index();
            $table->timestamp('synced_at')->nullable()->index();
            $table->enum('sync_status', ['pending', 'syncing', 'synced', 'failed', 'stale'])->default('pending')->index();
            $table->text('sync_error')->nullable();
            $table->json('extra_attributes')->nullable();
            $table->timestamps();
            $table->index(['product_id', 'is_available']);
            $table->index(['product_id', 'is_default']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
