<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('mobile', 32)->nullable();
            $table->string('country', 64)->nullable();
            $table->string('city', 64)->nullable();
            $table->string('district', 64)->nullable();
            $table->text('address');
            $table->string('postal_code', 16)->nullable();
            $table->boolean('is_default')->default(false)->index();
            $table->timestamps();
            $table->index(['user_id', 'is_default']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
