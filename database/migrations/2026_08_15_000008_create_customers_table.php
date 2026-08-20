<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('salla_id')->nullable()->unique();
            $table->string('salla_store_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('salla_connection_id')->nullable()->constrained('salla_connections')->nullOnDelete();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable()->index();
            $table->string('mobile', 32)->nullable()->index();
            $table->string('gender', 16)->nullable();
            $table->string('city', 64)->nullable();
            $table->string('country', 64)->nullable();
            $table->json('addresses')->nullable();
            $table->json('extra_attributes')->nullable();
            $table->string('source_updated_at')->nullable();
            $table->timestamp('synced_at')->nullable()->index();
            $table->string('sync_status', 16)->default('pending')->index();
            $table->timestamps();
            $table->index(['salla_store_id', 'salla_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
