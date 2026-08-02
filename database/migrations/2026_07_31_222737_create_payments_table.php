<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 100);
            $table->string('logo_path')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('config')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('payment_method_id')
                  ->constrained('payment_methods')
                  ->restrictOnDelete();
            $table->string('transaction_id', 255)->unique()->nullable();
            $table->string('gateway_payment_id', 255)->nullable()->unique();
            $table->decimal('amount', 12, 2);
            $table->char('currency', 3)->default('SAR');
            $table->enum('status', [
                'pending', 'paid', 'failed', 'refunded', 'partial_refunded',
            ])->default('pending');
            $table->json('gateway_response')->nullable();
            $table->string('failure_reason', 500)->nullable();
            $table->decimal('refunded_amount', 12, 2)->default(0);
            $table->unsignedTinyInteger('retry_count')->default(0);
            $table->timestamp('last_retry_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index('order_id');
            $table->index('status');
            $table->index('payment_method_id');
            $table->index(['status', 'expires_at']);
        });

        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->cascadeOnDelete();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->text('reason')->nullable();
            $table->enum('status', ['pending', 'processing', 'approved', 'rejected'])
                  ->default('pending');
            $table->string('gateway_refund_id', 255)->nullable()->unique();
            $table->timestamp('refunded_at')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('payment_id');
            $table->index('status');
        });

        // CHECK constraints
        DB::statement('
            ALTER TABLE payments 
            ADD CONSTRAINT chk_payment_refund_limit CHECK (
                refunded_amount <= amount
            )
        ');

        DB::statement('
            ALTER TABLE payments 
            ADD CONSTRAINT chk_payment_amount_positive CHECK (
                amount >= 0
            )
        ');

        DB::statement('
            ALTER TABLE refunds 
            ADD CONSTRAINT chk_refund_amount_positive CHECK (
                amount >= 0
            )
        ');
    }

    public function down(): void
    {
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('payment_methods');
    }
};
