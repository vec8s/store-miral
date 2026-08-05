<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('api_logs', function (Blueprint $table) {
            $table->id();
            $table->string('service', 32)->default('salla')->index();
            $table->string('method', 10)->index();
            $table->string('endpoint', 512);
            $table->unsignedInteger('status_code')->nullable()->index();
            $table->json('request_headers')->nullable();
            $table->json('request_body')->nullable();
            $table->json('response_headers')->nullable();
            $table->text('response_body')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->string('request_id', 64)->nullable()->index();
            $table->string('correlation_id', 64)->nullable()->index();
            $table->ipAddress('source_ip')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_error')->default(false)->index();
            $table->text('error_message')->nullable();
            $table->timestamp('occurred_at')->index();
            $table->timestamps();
            $table->index(['service', 'occurred_at']);
            $table->index(['status_code', 'occurred_at']);
            $table->index(['is_error', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_logs');
    }
};
