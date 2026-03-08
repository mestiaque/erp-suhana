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
        Schema::create('bank_btb_lcs', function (Blueprint $table) {
            $table->id();
            $table->string('lc_no')->unique();
            $table->foreignId('supplier_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('supplier_name')->nullable();
            $table->string('supplier_address')->nullable();
            $table->string('supplier_contact')->nullable();
            
            // LC Details
            $table->date('lc_open_date')->nullable();
            $table->date('lc_expiry_date')->nullable();
            $table->date('shipment_date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->foreignId('bank_id')->nullable()->constrained('users')->onDelete('set null'); // Bank as user
            $table->string('bank_name')->nullable();
            $table->string('branch_name')->nullable();
            
            // Amount Details
            $table->decimal('lc_value', 15, 2)->default(0);
            $table->decimal('used_value', 15, 2)->default(0);
            $table->decimal('remaining_value', 15, 2)->default(0);
            $table->string('currency', 10)->default('USD');
            $table->decimal('exchange_rate', 15, 2)->default(1);
            $table->decimal('lc_value_bdt', 15, 2)->default(0);
            
            // Status: 1=Pending, 2=Active, 3=Closed, 4=Cancelled
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
        Schema::dropIfExists('bank_btb_lcs');
    }
};
