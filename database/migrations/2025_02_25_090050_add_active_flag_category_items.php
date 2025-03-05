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
        Schema::table('category_items', function(Blueprint $table) {
            $table->string("active_flag", 2)->nullable(false)->default('Y')->after("name");
            $table->timestamp("inactive_date")->nullable()->after("active_flag")->after('active_flag'); 
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
