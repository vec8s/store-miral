<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->nullOnDelete();
            $table->unsignedBigInteger('shipping_method_id')->nullable();
            $table->string('order_number', 50)->unique();
            $table->enum('status', [
                'pending', 'confirmed', 'processing',
                'shipped', 'delivered', 'cancelled', 'refunded',
            ])->default('pending');
            $table->enum('payment_status', [
                'pending', 'paid', 'failed', 'refunded', 'partial_refunded',
            ])->default('pending');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('tax_amount', 12, 2)->default(0.00);
            $table->decimal('shipping_amount', 12, 2)->default(0.00);
            $table->decimal('discount_amount', 12, 2)->default(0.00);
            $table->decimal('total', 12, 2);
            $table->char('currency', 3)->default('SAR');
            $table->foreignId('shipping_address_id')
                  ->nullable()
                  ->constrained('addresses')
                  ->nullOnDelete();
            $table->foreignId('billing_address_id')
                  ->nullable()
                  ->constrained('addresses')
                  ->nullOnDelete();
            $table->text('notes')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->char('locale', 5)->default('ar');
            $table->timestamps();
            $table->timestamp('placed_at')->nullable();
            $table->softDeletes();

            $table->index('status');
            $table->index('payment_status');
            $table->index('user_id');
            $table->index('created_at');
            $table->index('placed_at');
            $table->index(['status', 'placed_at']);
            $table->index(['status', 'payment_status']);
            $table->index(['user_id', 'status']);
            $table->index('shipping_method_id');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('variant_id')
                  ->nullable()
                  ->constrained('product_variants')
                  ->nullOnDelete();
            $table->string('product_name', 191);
            $table->string('product_sku', 100)->nullable();
            $table->json('options')->nullable();
            $table->unsignedSmallInteger('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2);
            $table->decimal('total', 12, 2);
            $table->timestamps();

            $table->index('order_id');
            $table->index('product_id');
            $table->index(['order_id', 'product_id']);
        });

        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->enum('from_status', [
                'pending', 'confirmed', 'processing',
                'shipped', 'delivered', 'cancelled', 'refunded',
            ])->nullable();
            $table->enum('to_status', [
                'pending', 'confirmed', 'processing',
                'shipped', 'delivered', 'cancelled', 'refunded',
            ]);
            $table->text('comment')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['order_id', 'created_at']);
        });

        // CHECK constraints for financial integrity
        DB::statement('
            ALTER TABLE orders 
            ADD CONSTRAINT chk_order_total_calc CHECK (
                total = subtotal + tax_amount + shipping_amount - discount_amount
            )
        ');

        DB::statement('
            ALTER TABLE order_items 
            ADD CONSTRAINT chk_item_subtotal_calc CHECK (
                subtotal = unit_price * quantity
            )
        ');

        DB::statement('
            ALTER TABLE order_items 
            ADD CONSTRAINT chk_item_total_calc CHECK (
                total = subtotal + tax_amount - discount_amount
            )
        ');

        DB::statement('
            ALTER TABLE order_items 
            ADD CONSTRAINT chk_item_qty_positive CHECK (
                quantity > 0
            )
        ');
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_histories');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
