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
        Schema::create('export_realizations', function (Blueprint $table) {
            $table->id();
            $table->string('realization_no')->unique();
            $table->foreignId('export_lc_id')->nullable()->constrained('export_lcs')->onDelete('set null');
            $table->string('lc_no')->nullable();
            $table->foreignId('buyer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('buyer_name')->nullable();
            
            // Realization Details
            $table->date('submission_date')->nullable();
            $table->date('realization_date')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            
            // Amount Details
            $table->decimal('invoice_value', 15, 2)->default(0);
            $table->decimal('realized_value', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('bank_charges', 15, 2)->default(0);
            $table->decimal('net_realized', 15, 2)->default(0);
            $table->string('currency', 10)->default('USD');
            $table->decimal('exchange_rate', 15, 2)->default(1);
            $table->decimal('realized_in_bdt', 15, 2)->default(0);
            
            // Status: 1=Pending, 2=Partially Realized, 3=Fully Realized
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
        Schema::dropIfExists('export_realizations');
    }
};
