<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salla_webhook_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salla_connection_id')->nullable()->constrained('salla_connections')->nullOnDelete();
            $table->string('event_key')->unique();
            $table->string('event_name', 128)->index();
            $table->string('salla_order_id')->nullable()->index();
            $table->json('payload')->nullable();
            $table->string('payload_hash', 64)->index();
            $table->boolean('signature_valid')->default(true)->index();
            $table->timestamp('received_at')->index();
            $table->timestamp('processed_at')->nullable()->index();
            $table->timestamp('failed_at')->nullable();
            $table->unsignedSmallInteger('attempts')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamps();
            $table->index(['salla_order_id', 'event_name']);
            $table->index(['signature_valid', 'received_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salla_webhook_events');
    }
};
