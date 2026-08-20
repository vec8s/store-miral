<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salla_connections', function (Blueprint $table) {
            $table->id();
            $table->string('store_id')->unique();
            $table->string('merchant_id')->nullable();
            $table->text('access_token');
            $table->text('refresh_token')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->json('scopes')->nullable();
            $table->json('store_payload')->nullable();
            $table->string('status', 32)->default('connected')->index();
            $table->timestamp('last_successful_request_at')->nullable();
            $table->timestamp('last_refresh_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salla_connections');
    }
};
