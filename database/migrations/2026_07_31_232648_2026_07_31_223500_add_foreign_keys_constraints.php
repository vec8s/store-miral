<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ربط جدول الطلبات بطرق الشحن
        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('shipping_method_id')
                  ->references('id')
                  ->on('shipping_methods')
                  ->nullOnDelete();
        });

        // ربط جدول الشحنات بالطلبات فقط (تم ترك shipping_method_id لعدم التكرار لأنه مربوط في ملفه الأصلي)
        Schema::table('shipments', function (Blueprint $table) {
            $table->foreign('order_id')
                  ->references('id')
                  ->on('orders')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['shipping_method_id']);
        });
    }
};