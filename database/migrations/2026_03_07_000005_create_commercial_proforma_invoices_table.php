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
        Schema::create('commercial_proforma_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('pi_no')->unique();
            $table->foreignId('buyer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('buyer_name')->nullable();
            $table->string('buyer_address')->nullable();
            $table->string('buyer_contact')->nullable();
            
            // PI Details
            $table->date('pi_date')->nullable();
            $table->date('valid_until')->nullable();
            $table->string('payment_terms')->nullable();
            $table->string('delivery_terms')->nullable();
            $table->string('shipment_from')->nullable();
            $table->string('shipment_to')->nullable();
            
            // Amount Details
            $table->decimal('total_qty', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('commission', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2)->default(0);
            $table->string('currency', 10)->default('USD');
            
            // Status: 1=Draft, 2=Sent, 3=Confirmed, 4=Rejected, 5=Expired
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
        Schema::dropIfExists('commercial_proforma_invoices');
    }
};
