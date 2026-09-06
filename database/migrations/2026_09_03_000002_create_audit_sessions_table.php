<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection($this->auditConnection())->create(config('mestiaque_audit.sessions.table', 'es_audit_sessions'), function (Blueprint $table) {
            $table->id();
            $table->morphs('user');
            $table->string('session_token', 100)->unique();

            $table->string('ip_address', 45)->nullable();
            $table->char('country_code', 2)->nullable();
            $table->string('city', 100)->nullable();

            $table->string('device_type', 30)->nullable();
            $table->string('platform', 60)->nullable();
            $table->string('browser', 60)->nullable();
            $table->text('user_agent')->nullable();

            $table->dateTime('login_at');
            $table->dateTime('last_activity_at')->nullable()->index();
            $table->dateTime('logout_at')->nullable();

            $table->boolean('is_active')->default(true)->index();

            $table->unsignedBigInteger('login_audit_log_id')->nullable();

            $table->timestamps();

            $table->index(['user_type', 'user_id', 'is_active'], 'audit_sessions_user_active_index');
        });
    }

    public function down(): void
    {
        Schema::connection($this->auditConnection())->dropIfExists(config('mestiaque_audit.sessions.table', 'es_audit_sessions'));
    }

    protected function auditConnection(): ?string
    {
        return config('mestiaque_audit.storage.connection');
    }
};
