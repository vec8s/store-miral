<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->string('carrier_code', 50)->nullable();
            $table->string('name', 100);
            $table->string('logo_path')->nullable();
            $table->text('description')->nullable();
            $table->json('calculation_rules')->nullable();
            $table->unsignedInteger('estimated_days_min')->nullable();
            $table->unsignedInteger('estimated_days_max')->nullable();
            $table->decimal('price', 12, 2);
            $table->decimal('free_shipping_threshold', 12, 2)->nullable();
            $table->decimal('max_weight', 8, 2)->nullable();
            $table->decimal('min_order_amount', 12, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('is_active');
            $table->index(['is_active', 'sort_order']);
        });

        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('shipping_method_id')->nullable();
            $table->foreign('shipping_method_id')->references('id')->on('shipping_methods')->onDelete('restrict');
            $table->string('tracking_number', 100)->nullable()->unique();
            $table->string('carrier', 100)->nullable();
            $table->enum('status', [
                'preparing', 'shipped', 'in_transit', 'out_for_delivery', 'delivered', 'returned', 'failed',
            ])->default('preparing');
            $table->decimal('weight', 8, 2)->nullable();
            $table->decimal('shipping_cost', 12, 2)->nullable();
            $table->string('shipping_label_url', 500)->nullable();
            $table->decimal('insurance_amount', 12, 2)->nullable();
            $table->boolean('signature_required')->default(false);
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'status']);
        });

        Schema::create('shipment_tracking_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained('shipments')->cascadeOnDelete();
            $table->string('status', 50);
            $table->string('location', 255)->nullable();
            $table->text('description')->nullable();
            $table->timestamp('occurred_at');
            $table->json('raw_data')->nullable();
            $table->timestamps();

            $table->index(['shipment_id', 'occurred_at']);
        });

        // CHECK constraints
        DB::statement('
            ALTER TABLE shipping_methods 
            ADD CONSTRAINT chk_shipping_days CHECK (
                estimated_days_min IS NULL OR estimated_days_max IS NULL OR estimated_days_min <= estimated_days_max
            )
        ');

        DB::statement('
            ALTER TABLE shipping_methods 
            ADD CONSTRAINT chk_shipping_price_positive CHECK (
                price >= 0
            )
        ');
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_tracking_events');
        Schema::dropIfExists('shipments');
        Schema::dropIfExists('shipping_methods');
    }
};
