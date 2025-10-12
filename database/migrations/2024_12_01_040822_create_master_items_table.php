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
        Schema::create('master_items', function (Blueprint $table) {
            $table->id();
            $table->string("item_name", 100)->nullable(false)->unique("item_name_unique");
            $table->string("item_code", 50)->nullable(false)->unique("item_code_unique");
            $table->string("item_type", 50)->nullable(false);
            $table->foreignId("category_id")->constrained("category_items");
            $table->integer("cost_of_goods_sold")->nullable(false);
            $table->integer("selling_price")->nullable(false);
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
        Schema::dropIfExists('master_items');
    }
};
