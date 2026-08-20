<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checkout_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salla_connection_id')->nullable()->constrained('salla_connections')->nullOnDelete();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('cart_id')->constrained('carts')->cascadeOnDelete();
            $table->unsignedBigInteger('version')->default(1);
            $table->string('idempotency_key')->unique();
            $table->string('salla_cart_id')->nullable()->index();
            $table->text('checkout_url')->nullable();
            $table->json('amount_snapshot')->nullable();
            $table->string('currency', 3)->nullable();
            $table->unsignedBigInteger('cart_version')->nullable();
            $table->string('status', 32)->default('draft')->index();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checkout_sessions');
    }
};
