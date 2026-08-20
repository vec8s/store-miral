<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salla_connection_id')->nullable()->constrained('salla_connections')->nullOnDelete();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('salla_order_id')->nullable()->index();
            $table->string('source_event_id')->nullable();
            $table->string('version_hash', 64)->index();
            $table->string('status', 32)->index();
            $table->string('payment_status', 32)->index();
            $table->string('fulfillment_status', 32)->nullable();
            $table->unsignedBigInteger('total')->default(0);
            $table->string('currency', 3)->default('SAR');
            $table->json('customer_json')->nullable();
            $table->json('receiver_json')->nullable();
            $table->json('shipping_json')->nullable();
            $table->json('items_json')->nullable();
            $table->json('payments_json')->nullable();
            $table->json('shipments_json')->nullable();
            $table->longText('raw_payload_compressed')->nullable();
            $table->timestamp('captured_at')->index();
            $table->timestamps();
            $table->index(['order_id', 'version_hash']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_snapshots');
    }
};
