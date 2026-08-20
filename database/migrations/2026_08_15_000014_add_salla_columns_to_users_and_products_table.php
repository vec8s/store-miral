<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('phone_verified_at')->nullable()->after('phone');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('salla_connection_id')->nullable()->after('id')
                ->constrained('salla_connections')->nullOnDelete();
            $table->string('salla_product_id')->nullable()->after('salla_connection_id');
            $table->boolean('is_available')->default(true)->after('is_on_sale');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone_verified_at');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('salla_connection_id');
            $table->dropColumn(['salla_product_id', 'is_available']);
        });
    }
};
