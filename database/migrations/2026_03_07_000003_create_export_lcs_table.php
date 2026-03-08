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
        Schema::create('export_lcs', function (Blueprint $table) {
            $table->id();
            $table->string('lc_no')->unique();
            $table->foreignId('buyer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('buyer_name')->nullable();
            $table->string('buyer_address')->nullable();
            $table->string('buyer_contact')->nullable();
            
            // Export LC Details
            $table->date('lc_open_date')->nullable();
            $table->date('lc_expiry_date')->nullable();
            $table->date('shipment_date')->nullable();
            $table->string('issuing_bank')->nullable();
            $table->string('issuing_bank_branch')->nullable();
            $table->string('negotiating_bank')->nullable();
            
            // Amount Details
            $table->decimal('lc_value', 15, 2)->default(0);
            $table->decimal('realized_value', 15, 2)->default(0);
            $table->decimal('pending_value', 15, 2)->default(0);
            $table->string('currency', 10)->default('USD');
            
            // Status: 1=Pending, 2=Partially Realized, 3=Fully Realized, 4=Expired
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
        Schema::dropIfExists('export_lcs');
    }
};
