<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('variant_id')
                  ->nullable()
                  ->constrained('product_variants')
                  ->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedInteger('reserved_quantity')->default(0);
            $table->unsignedInteger('low_stock_threshold')->default(5);
            $table->boolean('low_stock_notified')->default(false);
            $table->string('warehouse_location', 100)->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'variant_id']);
            $table->index('warehouse_location');
            $table->index(['low_stock_threshold', 'low_stock_notified']);
        });

        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->constrained('inventories')->cascadeOnDelete();
            $table->enum('type', ['in', 'out', 'reserve', 'release', 'adjustment']);
            $table->integer('quantity');
            $table->string('reference_type', 100)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['reference_type', 'reference_id']);
            $table->index('type');
            $table->index(['inventory_id', 'created_at']);
        });

        // CHECK constraint: quantity >= reserved_quantity
        DB::statement('
            ALTER TABLE inventories 
            ADD CONSTRAINT chk_inventory_qty_reserved 
            CHECK (quantity >= reserved_quantity)
        ');
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
        Schema::dropIfExists('inventories');
    }
};
