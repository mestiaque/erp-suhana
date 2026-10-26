<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medias', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('src_id')->nullable();
            $table->boolean('src_type')->nullable()->default(false)->comment('0=media, 1=post, 2=category, 3=attribute, 4=Menus, 5=review, 6=Users 7=General, 8=expense, 9=transection, 10=Expense IOU, visit =11, Lead=12');
            $table->boolean('use_Of_file')->nullable()->default(false)->comment('0=media, 1=image, 2=banner, 3=gallery, 4=icon');
            $table->string('file_name')->nullable();
            $table->string('file_rename', 100)->nullable();
            $table->string('alt_text')->nullable();
            $table->string('caption')->nullable();
            $table->text('description')->nullable();
            $table->string('file_url', 191)->nullable();
            $table->string('file_url_sm', 100)->nullable();
            $table->string('file_url_md', 100)->nullable();
            $table->string('file_url_lg', 100)->nullable();
            $table->string('file_size', 100)->nullable();
            $table->integer('file_type')->default(0)->comment('0=unknown, 1=image, 2=pdf, 3=doc 4=Zip, rar, 5 = Vedio, 6=audio');
            $table->string('file_path', 50)->nullable();
            $table->integer('drag')->default(0);
            $table->bigInteger('addedby_id')->nullable();
            $table->bigInteger('editedby_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medias');
    }
};
