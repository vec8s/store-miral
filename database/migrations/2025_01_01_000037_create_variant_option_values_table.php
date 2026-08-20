<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_option_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_option_id')->constrained('product_options')->cascadeOnDelete();
            $table->string('value');
            $table->string('display_value')->nullable();
            $table->string('color_code', 16)->nullable();
            $table->string('image_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
            $table->index(['product_option_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_option_values');
    }
};
