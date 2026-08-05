<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('webhook_event_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webhook_event_id')
                ->constrained('webhook_events')
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('attempt_number')->default(1)->index();
            $table->enum('level', ['debug', 'info', 'warning', 'error', 'critical'])
                ->default('info')
                ->index();
            $table->string('stage', 64)->index();
            $table->text('message');
            $table->json('context')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->ipAddress('source_ip')->nullable();
            $table->timestamp('occurred_at')->useCurrent();
            $table->timestamps();

            $table->index(['webhook_event_id', 'occurred_at']);
            $table->index(['level', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_event_logs');
    }
};