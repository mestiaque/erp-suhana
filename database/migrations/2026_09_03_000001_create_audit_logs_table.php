<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection($this->auditConnection())->create(config('mestiaque_audit.storage.table', 'es_audit_logs'), function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->dateTime('occurred_at')->index();

            $table->nullableMorphs('actor');
            $table->string('actor_label')->nullable();

            $table->nullableMorphs('acting_as');
            $table->string('acting_as_label')->nullable();
            $table->boolean('is_impersonating')->default(false);

            $table->nullableMorphs('subject');
            $table->string('subject_label')->nullable();

            $table->string('module', 100)->nullable();
            $table->string('feature', 100)->nullable();
            $table->string('action', 100)->nullable();
            $table->string('event', 30)->index();
            $table->string('source', 20)->index();

            $table->string('ip_address', 45)->nullable();
            $table->char('country_code', 2)->nullable();
            $table->string('country_name', 100)->nullable();
            $table->string('region', 100)->nullable();
            $table->string('city', 100)->nullable();

            $table->text('user_agent')->nullable();
            $table->string('device_type', 30)->nullable();
            $table->string('platform', 60)->nullable();
            $table->string('browser', 60)->nullable();

            $table->string('url', 2048)->nullable();
            $table->string('method', 10)->nullable();
            $table->string('route_name', 150)->nullable();

            $table->uuid('request_id')->nullable()->index();
            $table->unsignedBigInteger('session_id')->nullable();

            $table->json('changes')->nullable();
            $table->string('summary', 500)->nullable();
            $table->json('request_payload')->nullable();
            $table->json('tags')->nullable();
            $table->json('meta')->nullable();

            $table->timestamps();

            $table->index(['module', 'feature']);
            $table->index('ip_address');
            $table->index('country_code');
            $table->index('session_id');
        });
    }

    public function down(): void
    {
        Schema::connection($this->auditConnection())->dropIfExists(config('mestiaque_audit.storage.table', 'es_audit_logs'));
    }

    protected function auditConnection(): ?string
    {
        return config('mestiaque_audit.storage.connection');
    }
};
