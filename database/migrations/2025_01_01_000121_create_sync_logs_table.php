<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sync_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sync_job_id')->constrained('sync_jobs')->cascadeOnDelete();
            $table->string('resource_type', 64)->index();
            $table->string('resource_id')->nullable()->index();
            $table->string('salla_id')->nullable()->index();
            $table->enum('action', ['create', 'update', 'delete', 'skip', 'error'])->index();
            $table->enum('status', ['success', 'failed', 'skipped', 'conflict'])->default('success')->index();
            $table->unsignedSmallInteger('attempt_number')->default(1);
            $table->unsignedInteger('duration_ms')->nullable();
            $table->json('before_state')->nullable();
            $table->json('after_state')->nullable();
            $table->text('error_message')->nullable();
            $table->json('error_context')->nullable();
            $table->ipAddress('source_ip')->nullable();
            $table->timestamp('occurred_at')->index();
            $table->timestamps();
            $table->index(['sync_job_id', 'status']);
            $table->index(['resource_type', 'salla_id']);
            $table->index(['status', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_logs');
    }
};
