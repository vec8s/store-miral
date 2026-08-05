<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('salla_id')->unique();
            $table->string('code')->index();
            $table->string('name')->nullable();
            $table->enum('discount_type', ['fixed', 'percentage'])->default('fixed');
            $table->unsignedBigInteger('discount_minor')->default(0);
            $table->string('currency', 3)->default('SAR');
            $table->unsignedInteger('discount_percentage')->nullable();
            $table->unsignedBigInteger('min_order_minor')->nullable();
            $table->string('min_order_currency', 3)->default('SAR');
            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('usage_limit_per_customer')->nullable();
            $table->unsignedInteger('usage_count')->default(0);
            $table->timestamp('starts_at')->nullable()->index();
            $table->timestamp('expires_at')->nullable()->index();
            $table->enum('status', ['active', 'expired', 'disabled'])->default('active')->index();
            $table->string('source_updated_at')->nullable()->index();
            $table->timestamp('synced_at')->nullable()->index();
            $table->enum('sync_status', ['pending', 'syncing', 'synced', 'failed', 'stale'])->default('pending')->index();
            $table->json('extra_attributes')->nullable();
            $table->timestamps();
            $table->index(['status', 'starts_at', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
