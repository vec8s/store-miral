<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100); // "الرياض", "جدة", "المناطق البعيدة"
            $table->json('countries')->nullable(); // ["SA"]
            $table->json('regions')->nullable(); // ["Riyadh", "Makkah"]
            $table->json('postal_codes')->nullable(); // ["10000-15000"]
            $table->json('cities')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('is_active');
            $table->index('is_default');
        });

        Schema::create('shipping_zone_methods', function (Blueprint $table) {
            $table->foreignId('zone_id')->constrained('shipping_zones')->cascadeOnDelete();
            $table->foreignId('method_id')->constrained('shipping_methods')->cascadeOnDelete();
            $table->decimal('price', 12, 2);
            $table->decimal('free_shipping_threshold', 12, 2)->nullable();
            $table->decimal('max_weight', 8, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->primary(['zone_id', 'method_id']);
            $table->index(['zone_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_zone_methods');
        Schema::dropIfExists('shipping_zones');
    }
};
