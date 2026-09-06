<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::connection($this->auditConnection())->getConnection()->getDriverName();

        // SQLite (used by the test suite / Testbench) does not support
        // adding foreign keys to an existing table via ALTER TABLE.
        if ($driver === 'sqlite') {
            return;
        }

        Schema::connection($this->auditConnection())->table(config('mestiaque_audit.storage.table', 'es_audit_logs'), function (Blueprint $table) {
            $table->foreign('session_id')
                ->references('id')->on(config('mestiaque_audit.sessions.table', 'es_audit_sessions'))
                ->nullOnDelete();
        });

        Schema::connection($this->auditConnection())->table(config('mestiaque_audit.sessions.table', 'es_audit_sessions'), function (Blueprint $table) {
            $table->foreign('login_audit_log_id')
                ->references('id')->on(config('mestiaque_audit.storage.table', 'es_audit_logs'))
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        $driver = Schema::connection($this->auditConnection())->getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            return;
        }

        Schema::connection($this->auditConnection())->table(config('mestiaque_audit.storage.table', 'es_audit_logs'), function (Blueprint $table) {
            $table->dropForeign(['session_id']);
        });

        Schema::connection($this->auditConnection())->table(config('mestiaque_audit.sessions.table', 'es_audit_sessions'), function (Blueprint $table) {
            $table->dropForeign(['login_audit_log_id']);
        });
    }

    protected function auditConnection(): ?string
    {
        return config('mestiaque_audit.storage.connection');
    }
};
