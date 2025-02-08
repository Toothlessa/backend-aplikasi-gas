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
            $table->bigInteger('owner_id')->unsigned()->nullable(false);
            $table->string("asset_name", 100)->nullable(false)->unique("asset_name_unique");
            $table->integer("quantity")->nullable(false);
            $table->integer("cogs")->nullable(false);
            $table->integer("selling_price")->nullable(false);
            $table->string("description", 100)->nullable();
            $table->integer("created_by")->nullable();
            $table->integer("updated_by")->nullable();
            $table->timestamps();
            $table->foreign("owner_id")->on("asset_owners")->references("id");
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
