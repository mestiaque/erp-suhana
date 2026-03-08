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
        Schema::create('commercial_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique();
            $table->foreignId('buyer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('buyer_name')->nullable();
            $table->string('buyer_address')->nullable();
            $table->string('buyer_contact')->nullable();
            
            // Invoice Details
            $table->date('invoice_date')->nullable();
            $table->date('shipment_date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->string('lc_no')->nullable();
            $table->date('lc_date')->nullable();
            $table->string('pi_no')->nullable();
            
            // Shipping Details
            $table->string('shipment_from')->nullable();
            $table->string('shipment_to')->nullable();
            $table->string('country_of_origin')->nullable();
            $table->string('destination_country')->nullable();
            $table->string('carrier')->nullable();
            $table->string('vessel_flight_no')->nullable();
            $table->string('container_no')->nullable();
            $table->string('seal_no')->nullable();
            $table->string('marks_no')->nullable();
            $table->string('description_of_goods')->nullable();
            
            // Amount Details
            $table->decimal('total_qty', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('tax', 15, 2)->default(0);
            $table->decimal('shipping_cost', 15, 2)->default(0);
            $table->decimal('insurance', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->string('currency', 10)->default('USD');
            $table->decimal('exchange_rate', 15, 2)->default(1);
            $table->decimal('total_in_bdt', 15, 2)->default(0);
            
            // Status
            $table->tinyInteger('status')->default(1)->comment('1=Pending, 2=Approved, 3=Shipped, 4=Delivered, 5=Cancelled');
            $table->text('remarks')->nullable();
            
            // User tracking
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('edited_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commercial_invoices');
    }
};
