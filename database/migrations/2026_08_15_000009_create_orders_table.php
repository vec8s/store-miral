<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('public_id')->unique();
            $table->foreignId('salla_connection_id')->nullable()->constrained('salla_connections')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('checkout_session_id')->nullable()->constrained('checkout_sessions')->nullOnDelete();
            $table->string('salla_id')->nullable()->unique();
            $table->string('salla_order_id')->nullable();
            $table->string('reference_id')->nullable()->index();
            $table->string('local_status', 32)->default('pending')->index();
            $table->string('salla_status', 32)->nullable()->index();
            $table->string('payment_status', 32)->default('pending')->index();
            $table->string('fulfillment_status', 32)->nullable()->index();
            $table->string('shipping_status', 32)->nullable();
            $table->string('payment_method', 32)->nullable();
            $table->string('currency', 3)->default('SAR');
            $table->unsignedBigInteger('subtotal_minor')->default(0);
            $table->unsignedBigInteger('total_minor')->default(0);
            $table->unsignedBigInteger('shipping_cost_minor')->default(0);
            $table->unsignedBigInteger('tax_amount_minor')->default(0);
            $table->unsignedBigInteger('discount_minor')->default(0);
            $table->json('shipping_address')->nullable();
            $table->json('billing_address')->nullable();
            $table->json('extra_attributes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('source_updated_at')->nullable()->index();
            $table->timestamp('last_salla_updated_at')->nullable();
            $table->timestamp('placed_at')->nullable();
            $table->timestamp('synced_at')->nullable()->index();
            $table->string('sync_status', 16)->default('pending')->index();
            $table->text('sync_error')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
            $table->index(['salla_connection_id', 'salla_order_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
