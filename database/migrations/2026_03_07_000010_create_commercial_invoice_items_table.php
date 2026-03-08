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
        Schema::create('commercial_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->integer('item_no')->nullable();
            $table->text('description')->nullable();
            $table->string('hs_code', 50)->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->decimal('quantity', 15, 2)->default(0);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('total_price', 15, 2)->default(0);
            $table->string('carton_qty', 50)->nullable();
            $table->string('carton_no', 50)->nullable();
            $table->decimal('net_weight', 15, 2)->nullable();
            $table->decimal('gross_weight', 15, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('invoice_id')->references('id')->on('commercial_invoices')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('attributes')->onDelete('set null');
            
            $table->index('invoice_id');
            $table->index('unit_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commercial_invoice_items');
    }
};
