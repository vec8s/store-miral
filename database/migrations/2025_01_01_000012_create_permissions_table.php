<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 128)->unique();
            $table->string('name');
            $table->string('group', 64)->default('general')->index();
            $table->string('description')->nullable();
            $table->boolean('is_protected')->default(false);
            $table->timestamps();
            $table->index(['group', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
