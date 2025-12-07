<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreign(['order_id'], 'order_items_order_id_fkey')->references(['id'])->on('orders')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['pizza_id'], 'order_items_pizza_id_fkey')->references(['id'])->on('pizzas')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign('order_items_order_id_fkey');
            $table->dropForeign('order_items_pizza_id_fkey');
        });
    }
};
