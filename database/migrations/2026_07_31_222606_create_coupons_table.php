<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->collation('utf8mb4_bin');
            $table->enum('type', ['fixed', 'percentage', 'free_shipping', 'buy_x_get_y', 'tiered']);
            $table->decimal('value', 12, 2);
            $table->decimal('min_order_amount', 12, 2)->nullable();
            $table->decimal('max_discount_amount', 12, 2)->nullable();
            $table->unsignedInteger('max_uses')->nullable();
            $table->unsignedInteger('max_uses_per_user')->nullable();
            $table->unsignedInteger('uses_count')->default(0);
            $table->json('applies_to')->nullable();
            $table->json('exclude_products')->nullable();
            $table->text('description')->nullable();
            $table->string('banner_image')->nullable();
            $table->boolean('is_stackable')->default(false);
            $table->unsignedInteger('priority')->default(0);
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
            $table->index('expires_at');
            $table->index(['code', 'is_active']);
            $table->index(['is_active', 'starts_at', 'expires_at']);
        });

        // CHECK constraint for value based on type
        DB::statement('
            ALTER TABLE coupons 
            ADD CONSTRAINT chk_coupon_value_type CHECK (
                (type = "percentage" AND value BETWEEN 0 AND 100) OR
                (type = "fixed" AND value >= 0) OR
                (type = "free_shipping" AND value = 0) OR
                (type IN ("buy_x_get_y", "tiered") AND value >= 0)
            )
        ');
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
