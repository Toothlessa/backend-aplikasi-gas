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
        Schema::create('asset_owners', function (Blueprint $table) {
            $table->id();
            $table->string('name', length: 100)->nullable()->unique("name_asset_owner_unique");
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
        Schema::dropIfExists('asset_owners');
    }
};
