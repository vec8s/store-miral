<?php

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
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->string('image_path', 255);
            $table->string('alt_text', 255)->nullable();
            $table->string('image_type', 20)->default('gallery'); // main, gallery, thumbnail
            $table->unsignedInteger('file_size')->nullable();
            $table->string('mime_type', 50)->nullable();
            $table->unsignedSmallInteger('width')->nullable();
            $table->unsignedSmallInteger('height')->nullable();
            $table->smallInteger('sort_order')->unsigned()->default(0);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['product_id', 'sort_order']);
            $table->index(['product_id', 'is_primary']);
            $table->index(['variant_id', 'is_primary']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
