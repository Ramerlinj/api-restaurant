<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('custom_pizza_ingredients', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->bigInteger('custom_pizza_id');
            $table->bigInteger('ingredient_id');
            $table->integer('quantity')->nullable()->default(1);
            $table->timestampTz('created_at')->default(DB::raw("now()"));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_pizza_ingredients');
    }
};
