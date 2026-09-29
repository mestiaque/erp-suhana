<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mail_notification_settings', function (Blueprint $table) {
            $table->id();
            $table->string('action_key')->unique();
            $table->string('title');
            $table->boolean('is_enabled')->default(true);
            // Null = use the action's default recipients. A non-empty array
            // overrides the default and sends only to these user ids.
            $table->json('recipient_user_ids')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mail_notification_settings');
    }
};
