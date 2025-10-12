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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId("owner_id")->constrained("asset_owners");
            $table->foreignId("item_id")->constrained("master_items");
            $table->integer("quantity")->nullable(false);
            $table->integer("cogs")->nullable(false);
            $table->integer("selling_price")->nullable(false);
            $table->string("description", 100)->nullable();
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
        Schema::dropIfExists('assets');
    }
};
