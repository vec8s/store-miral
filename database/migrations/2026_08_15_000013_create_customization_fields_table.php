<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customization_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('key', 64)->index();
            $table->string('label');
            $table->string('type', 16)->default('text');
            $table->boolean('required')->default(false);
            $table->unsignedInteger('max_length')->nullable();
            $table->json('validation_rules')->nullable();
            $table->json('options_json')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['product_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customization_fields');
    }
};
