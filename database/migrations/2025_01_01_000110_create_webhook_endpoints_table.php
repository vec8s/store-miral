<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('webhook_endpoints', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('url');
            $table->text('secret');
            $table->string('algorithm', 16)->default('sha256');
            $table->json('subscribed_events');
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('timeout_seconds')->default(30);
            $table->unsignedInteger('max_retries')->default(5);
            $table->text('description')->nullable();
            $table->timestamp('last_triggered_at')->nullable()->index();
            $table->unsignedInteger('total_deliveries')->default(0);
            $table->unsignedInteger('failed_deliveries')->default(0);
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_endpoints');
    }
};
