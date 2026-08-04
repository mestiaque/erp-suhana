<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A user's signature image, used on printed inventory documents
     * (Requisition, Store Delivery Challan) wherever that user approved or
     * authorized something — set via inv_signature.edit self-service page.
     * Lives on the host `users` table since signatures are shared across
     * every module, not just inventory.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'signature')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('signature')->nullable()->after('email');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'signature')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('signature');
            });
        }
    }
};
