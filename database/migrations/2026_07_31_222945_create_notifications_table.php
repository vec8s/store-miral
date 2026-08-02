<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->numericMorphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->string('channel', 20)->default('database');
            $table->unsignedTinyInteger('priority')->default(0);
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index('channel');
            $table->index('read_at');
            $table->index('type');
            $table->index('priority');
            $table->index('expires_at');
            $table->index(['notifiable_id', 'notifiable_type', 'read_at'], 'idx_notif_unread');
            $table->index(['notifiable_id', 'notifiable_type', 'type', 'read_at'], 'idx_notif_type_unread');
            $table->index(['notifiable_id', 'notifiable_type', 'created_at'], 'idx_notif_recent');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
