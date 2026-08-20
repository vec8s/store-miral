<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhook_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webhook_endpoint_id')->nullable()->constrained('webhook_endpoints')->nullOnDelete();
            $table->string('event_id')->unique();
            $table->string('event_type')->index();
            $table->string('resource_type', 64)->nullable()->index();
            $table->string('resource_id')->nullable()->index();
            $table->json('payload');
            $table->json('headers')->nullable();
            $table->string('source_ip', 45)->nullable();
            $table->string('signature', 128)->nullable();
            $table->enum('status', ['received', 'verified', 'processing', 'processed', 'failed', 'rejected', 'expired'])->default('received')->index();
            $table->unsignedSmallInteger('attempts')->default(0);
            $table->unsignedInteger('response_status')->nullable();
            $table->text('response_body')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('received_at')->index();
            $table->timestamp('processed_at')->nullable()->index();
            $table->timestamp('failed_at')->nullable();
            $table->json('processing_log')->nullable();
            $table->timestamps();
            $table->index(['event_type', 'status']);
            $table->index(['resource_type', 'resource_id']);
            $table->index(['status', 'received_at']);
            $table->index(['received_at', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_events');
    }
};
