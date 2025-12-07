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
        Schema::create('users', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('name', 100)->nullable();
            $table->string('email', 150)->unique('users_email_key');
            $table->string('password', 200);
            $table->string('phone', 30)->nullable();
            $table->timestamp('created_at')->nullable()->default(DB::raw("now()"));
        });
        DB::statement("alter table \"users\" add column \"role\" user_role not null default 'user'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
