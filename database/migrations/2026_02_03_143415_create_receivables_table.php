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
        Schema::create('receivables', function (Blueprint $table) {
            $table->id();
            $table->foreignId("customer_id")->constrained("customers")->nullable(false);
            $table->string("invoice_number", length: 50)->nullable(false)->unique("invoice_number_unique");
            $table->string("status", length: 20)->nullable(false);
            $table->string('description', length: 100)->nullable();
            $table->dateTime("invoice_date")->nullable(false);
            $table->dateTime("due_date")->nullable(false);
            $table->integer("total_amount")->nullable(false);
            $table->integer("paid_amount")->nullable(false);
            $table->integer("remaining_amount")->nullable(false);
            $table->string("source_type", length: 20)->nullable(false);
            $table->unsignedBigInteger("source_id")->nullable(false);
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
        Schema::dropIfExists('receivables');
    }
};
