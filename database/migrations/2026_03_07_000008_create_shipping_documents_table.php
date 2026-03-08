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
        Schema::create('shipping_documents', function (Blueprint $table) {
            $table->id();
            $table->string('doc_no')->unique();
            $table->foreignId('invoice_id')->nullable()->constrained('commercial_invoices')->onDelete('set null');
            $table->string('invoice_no')->nullable();
            $table->foreignId('buyer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('buyer_name')->nullable();
            
            // Document Details
            $table->date('issue_date')->nullable();
            $table->string('shipment_type')->nullable(); // Air, Sea, Land
            $table->string(' vessel_name')->nullable();
            $table->string('flight_no')->nullable();
            $table->date('departure_date')->nullable();
            $table->date('arrival_date')->nullable();
            $table->string('port_of_loading')->nullable();
            $table->string('port_of_discharge')->nullable();
            $table->string('country_of_origin')->nullable();
            $table->string('destination_country')->nullable();
            
            // Document Types
            $table->string('bl_awb_no')->nullable();
            $table->date('bl_awb_date')->nullable();
            $table->string('commercial_invoice_no')->nullable();
            $table->string('packing_list_no')->nullable();
            $table->string('certificate_of_origin')->nullable();
            $table->string('gsp_form')->nullable();
            $table->string('inspection_certificate')->nullable();
            $table->string('insurance_policy')->nullable();
            
            // Status: 1=Pending, 2=Submitted, 3=Approved, 4=Rejected
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('shipping_documents');
    }
};
