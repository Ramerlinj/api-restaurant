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
        Schema::create('ingredients', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('name', 100);
            $table->decimal('price', 10);
            $table->jsonb('image_url')->nullable();
            $table->boolean('available')->nullable()->default(false);
        });
        DB::statement("alter table \"ingredients\" add column \"ingredient\" ingredient_type not null");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
