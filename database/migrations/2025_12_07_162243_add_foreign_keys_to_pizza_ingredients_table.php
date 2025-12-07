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
        Schema::table('pizza_ingredients', function (Blueprint $table) {
            $table->foreign(['ingredient_id'], 'pizza_ingredients_ingredient_id_fkey')->references(['id'])->on('ingredients')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['pizza_id'], 'pizza_ingredients_pizza_id_fkey')->references(['id'])->on('pizzas')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pizza_ingredients', function (Blueprint $table) {
            $table->dropForeign('pizza_ingredients_ingredient_id_fkey');
            $table->dropForeign('pizza_ingredients_pizza_id_fkey');
        });
    }
};
