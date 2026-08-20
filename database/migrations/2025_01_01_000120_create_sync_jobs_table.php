<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sync_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('resource_type', 64)->index();
            $table->enum('sync_type', ['full', 'partial', 'incremental', 'webhook', 'manual', 'retry'])->index();
            $table->enum('status', ['pending', 'running', 'completed', 'failed', 'cancelled', 'paused'])->default('pending')->index();
            $table->unsignedInteger('total_items')->default(0);
            $table->unsignedInteger('processed_items')->default(0);
            $table->unsignedInteger('successful_items')->default(0);
            $table->unsignedInteger('failed_items')->default(0);
            $table->unsignedInteger('batch_size')->default(100);
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->unsignedTinyInteger('max_attempts')->default(5);
            $table->json('filters')->nullable();
            $table->json('metadata')->nullable();
            $table->text('error_message')->nullable();
            $table->text('failure_context')->nullable();
            $table->timestamp('started_at')->nullable()->index();
            $table->timestamp('completed_at')->nullable()->index();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('next_retry_at')->nullable()->index();
            $table->integer('duration_seconds')->nullable();
            $table->foreignId('triggered_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('triggered_by_type', 32)->nullable();
            $table->string('triggered_by_source')->nullable();
            $table->timestamps();
            $table->index(['status', 'started_at']);
            $table->index(['resource_type', 'status']);
            $table->index(['status', 'next_retry_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_jobs');
    }
};
