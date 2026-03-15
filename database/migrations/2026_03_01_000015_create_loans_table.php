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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('type'); // personal, house, car, emergency
            $table->decimal('principal_amount', 12, 2)->default(0);
            $table->decimal('interest_rate', 5, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('monthly_installment', 12, 2)->default(0);
            $table->integer('total_installments')->default(1);
            $table->integer('paid_installments')->default(0);
            $table->integer('remaining_installments')->default(1);
            $table->date('disbursement_date')->nullable();
            $table->date('first_installment_date')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'active', 'completed'])->default('pending');
            $table->text('reason')->nullable();
            $table->text('admin_remark')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
