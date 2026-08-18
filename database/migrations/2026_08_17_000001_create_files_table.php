<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Central, dynamic, polymorphic file storage table.
     *
     * Every model that owns an uploaded file (User, Attribute, HrEmployee, etc.)
     * is linked here via fileable_type + fileable_id, the same way Laravel's
     * built-in polymorphic relations work. use_case differentiates multiple
     * files/roles on the same owner (profile, banner, gallery, documents, ...).
     */
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();

            // Polymorphic owner of the file (e.g. App\Models\User, App\Models\Attribute, ME\Hr\Models\HrEmployee)
            $table->string('fileable_type')->nullable();
            $table->unsignedBigInteger('fileable_id')->nullable();

            // Logical role of the file for its owner: profile, banner, gallery, documents, signature, photo, general, ...
            $table->string('use_case')->default('general');

            // Randomly generated (UUID based) on-disk file name
            $table->string('file_name');

            // Original client-uploaded file name, kept for reference/download
            $table->string('original_name')->nullable();

            // Relative path to the file on its disk
            $table->string('file_path')->nullable();

            // Complete URL / absolute path to the file, ready to use directly (e.g. in <img src>)
            $table->text('file_full_path')->nullable();

            // Optional resized variants (legacy parity with sm/md/lg thumbnails)
            $table->string('file_path_sm')->nullable();
            $table->string('file_path_md')->nullable();
            $table->string('file_path_lg')->nullable();

            // Which filesystem disk (config/filesystems.php) the file lives on
            $table->string('disk')->default('public');

            $table->string('alt_text')->nullable();
            $table->string('caption')->nullable();
            $table->text('description')->nullable();

            // Mime type of the file, e.g. image/jpeg, application/pdf
            $table->string('file_type')->nullable();
            $table->string('extension', 20)->nullable();
            $table->unsignedBigInteger('size')->nullable();

            // For ordering multi-file use cases (gallery, documents)
            $table->integer('sort_order')->default(0);

            // Free-form extra data (legacy identifiers, dimensions, etc.) — keeps the table dynamic/extensible
            $table->json('meta')->nullable();

            // Bookkeeping for the media -> files sync so it stays idempotent/re-runnable
            $table->string('source_table')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();

            $table->unsignedBigInteger('addedby_id')->nullable();
            $table->unsignedBigInteger('editedby_id')->nullable();
            $table->timestamps();

            $table->index(['fileable_type', 'fileable_id', 'use_case']);
            $table->unique(['source_table', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
