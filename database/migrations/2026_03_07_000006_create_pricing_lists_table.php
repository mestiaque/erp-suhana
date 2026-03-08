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
        Schema::create('pricing_lists', function (Blueprint $table) {
            $table->id();
            $table->string('price_list_no')->unique();
            $table->foreignId('buyer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('buyer_name')->nullable();
            
            // Pricing List Details
            $table->date('effective_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('season')->nullable();
            $table->string('year')->nullable();
            
            // Status: 1=Active, 2=Expired, 3=Cancelled
            $table->tinyInteger('status')->default(1);
            $table->text('remarks')->nullable();
            
            // User tracking
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('edited_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            $table->softDeletes();
        });
        
        // Create pricing list items table
        Schema::create('pricing_list_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pricing_list_id')->constrained('pricing_lists')->onDelete('cascade');
            $table->string('item_name')->nullable();
            $table->string('item_code')->nullable();
            $table->string('description')->nullable();
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('moq', 15, 2)->default(0);
            $table->string('currency', 10)->default('USD');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing_list_items');
        Schema::dropIfExists('pricing_lists');
    }
};
