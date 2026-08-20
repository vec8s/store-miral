<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('url');
            $table->string('thumbnail_url')->nullable();
            $table->string('medium_url')->nullable();
            $table->string('large_url')->nullable();
            $table->string('alt_text')->nullable();
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->boolean('is_main')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->string('source_updated_at')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
            $table->index(['product_id', 'sort_order']);
            $table->index(['product_id', 'is_main']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
