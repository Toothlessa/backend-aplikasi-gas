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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string("customer_name", 100)->nullable(false);
            $table->string("customer_type", 5)->nullable(false);
            $table->string("nik")->nullable()->unique("customer_nik_unique");
            $table->string("email", 50)->nullable();
            $table->string("address", 100)->nullable();
            $table->string("phone", 20)->nullable();
            $table->enum('active_flag', ['Y', 'N'])->default('Y');
            $table->timestamp("inactive_date")->nullable();
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
        Schema::dropIfExists('customers');
    }
};
