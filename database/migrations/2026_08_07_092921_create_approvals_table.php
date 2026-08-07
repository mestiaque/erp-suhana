<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('approvals')) {
            return;
        }

        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->index();

            // Which module/feature this approval belongs to, e.g. "hr.leave_request".
            // Modules are registered in config/approval.php.
            $table->string('module')->index();

            // Optional link to the actual record being approved.
            $table->string('approvable_type')->nullable();
            $table->unsignedBigInteger('approvable_id')->nullable();

            $table->string('title');
            $table->text('description')->nullable();

            $table->string('status', 20)->default('pending')->index(); // pending, approved, rejected, cancelled

            // Direct link to the module's own page for this approval.
            // Prefer a named route (resilient to domain changes); fallback_url is used if no route.
            $table->string('route_name')->nullable();
            $table->json('route_params')->nullable();
            $table->string('fallback_url', 1024)->nullable();

            $table->unsignedBigInteger('requested_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('remarks')->nullable();

            $table->json('meta')->nullable();

            $table->timestamps();

            $table->index(['approvable_type', 'approvable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approvals');
    }
};
