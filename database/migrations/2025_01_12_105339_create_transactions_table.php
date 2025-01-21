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
            $table->string("description", 100);
            $table->bigInteger('item_id')->unsigned();
            // $table->bigInteger('stock_id')->unsigned();
            $table->bigInteger('customer_id')->unsigned();
            $table->integer("created_by")->nullable();
            $table->integer("updated_by")->nullable();
            $table->timestamps();
            $table->foreign("item_id")->on("master_items")->references("id");
            // $table->foreign("stock_id")->on("stock_items")->references("id");
            $table->foreign("customer_id")->on("customers")->references("id");
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
