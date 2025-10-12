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
        Schema::create('category_items', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->nullable(false)->unique("name_category_item_unique");
            $table->string('prefix', 5)->unique('prefix_category_item_unique');
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
        Schema::dropIfExists('category_items');
    }
};
