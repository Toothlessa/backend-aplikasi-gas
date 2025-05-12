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

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string("username", 50)->nullable(false)->unique("usename_users_unique");
            $table->string("password", length:100)->nullable(false);
            $table->string("email", 50)->nullable(false);
            $table->string("phone", 20)->nullable();
            $table->string("token", length:100)->nullable();
            $table->integer("expiresIn")->nullable();
            $table->integer("created_by")->nullable();
            $table->integer("updated_by")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_users');
    }
};
