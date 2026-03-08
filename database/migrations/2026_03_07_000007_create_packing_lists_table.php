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
        Schema::create('packing_lists', function (Blueprint $table) {
            $table->id();
            $table->string('packing_list_no')->unique();
            $table->foreignId('invoice_id')->nullable()->constrained('commercial_invoices')->onDelete('set null');
            $table->string('invoice_no')->nullable();
            $table->foreignId('buyer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('buyer_name')->nullable();
            
            // Packing List Details
            $table->date('packing_date')->nullable();
            $table->date('shipment_date')->nullable();
            $table->string('shipment_from')->nullable();
            $table->string('shipment_to')->nullable();
            $table->string('vessel_flight_no')->nullable();
            $table->string('container_no')->nullable();
            $table->string('seal_no')->nullable();
            
            // Summary
            $table->integer('total_cartons')->default(0);
            $table->decimal('net_weight', 15, 2)->default(0);
            $table->decimal('gross_weight', 15, 2)->default(0);
            $table->decimal('total_volume', 15, 4)->default(0);
            
            // Status: 1=Draft, 2=Packed, 3=Shipped
            $table->tinyInteger('status')->default(1);
            $table->text('remarks')->nullable();
            
            // User tracking
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('edited_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            $table->softDeletes();
        });
        
        // Create packing list items table
        Schema::create('packing_list_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('packing_list_id')->constrained('packing_lists')->onDelete('cascade');
            $table->string('item_description')->nullable();
            $table->string('style_no')->nullable();
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            $table->integer('carton_qty')->default(0);
            $table->integer('pcs_per_carton')->default(0);
            $table->integer('total_pcs')->default(0);
            $table->decimal('unit_nw', 10, 2)->default(0);
            $table->decimal('unit_gw', 10, 2)->default(0);
            $table->decimal('carton_measurements', 20, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packing_list_items');
        Schema::dropIfExists('packing_lists');
    }
};
