<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('salla_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('merchant_id')->unique();
            $table->text('access_token');
            $table->text('refresh_token')->nullable();
            $table->string('token_type', 32)->default('Bearer');
            $table->string('scope')->nullable();
            $table->timestamp('access_token_expires_at')->nullable();
            $table->timestamp('refresh_token_expires_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salla_tokens');
    }
};
