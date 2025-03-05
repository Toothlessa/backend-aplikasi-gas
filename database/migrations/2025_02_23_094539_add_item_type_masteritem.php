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
        Schema::table('master_items', function(Blueprint $table) {
            $table->string("item_type", 50)->nullable(false)->default('ITEM')->after("item_code");
            $table->bigInteger('category_id')->unsigned()->nullable(false)->after('item_type');
            $table->foreign("category_id")->on("category_items")->references("id");
            $table->dropColumn('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
    }
};
