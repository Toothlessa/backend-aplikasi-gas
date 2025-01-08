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
        Schema::table('users', function(Blueprint $table) {
            $table->renameColumn("name", "fullname");
            $table->string("email", 50)->nullable()->unique("users_email_unique")->after("fullname");
            $table->string("phone", 20)->nullable()->unique("users_phone_unique")->after("email");
            $table->string("street", 100)->nullable()->after("phone");
            $table->string("city", 50)->nullable()->after("street");
            $table->string("province", 50)->nullable()->after("city");
            $table->string("postal_code", 20)->nullable()->after("province");
            $table->string("country", 50)->nullable()->after("postal_code");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
