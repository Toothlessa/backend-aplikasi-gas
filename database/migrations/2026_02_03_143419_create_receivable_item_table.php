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
        Schema::create('receivable_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId("receivable_id")->constrained("receivables")->nullable(false);
            $table->foreignId("item_id")->constrained("master_items")->nullable(false);
            $table->integer("qty")->nullable(false);
            $table->integer("price")->nullable(false);
            $table->integer("subtotal")->nullable(false);
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
        Schema::dropIfExists('receivable_items');
    }
};
