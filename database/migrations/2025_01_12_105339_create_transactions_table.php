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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string("trx_number", 100)->nullable(false)->unique("trx_transacton_unique");
            $table->integer("quantity");
            $table->integer("amount");
            $table->integer("total");
            $table->string("description", 100)->nullable();
            $table->foreignId("item_id")->constrained("master_items");
            $table->foreignId("stock_id")->constrained("stock_items");
            $table->foreignId("customer_id")->constrained("customers");
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
        Schema::dropIfExists('transactions');
    }
};
