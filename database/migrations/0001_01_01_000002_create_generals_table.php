<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Single-row app settings (site info, mail/SMS config, logos).
    public function up(): void
    {
        Schema::create('generals', function (Blueprint $table) {
            $table->id();
            $table->string('company_s_code', 20)->nullable();
            $table->string('title', 100)->nullable();
            $table->string('subtitle', 200)->nullable();
            $table->string('website', 100)->nullable();
            $table->string('logo', 100)->nullable();
            $table->string('report_logo')->nullable();
            $table->string('favicon', 100)->nullable();
            $table->string('signature', 100)->nullable();
            $table->json('hidden_permission_modules')->nullable();
            $table->string('mobile', 100)->nullable();
            $table->string('mobile2', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('email2', 100)->nullable();
            $table->text('address_one')->nullable();
            $table->text('address_two')->nullable();
            $table->string('postal_address', 191)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('city', 50)->nullable();
            $table->string('state', 50)->nullable();
            $table->string('division', 50)->nullable();
            $table->string('country', 50)->nullable();
            $table->boolean('commingsoon_mode')->default(false);
            $table->boolean('notification_status')->default(false);
            $table->string('fb_pageId', 100)->nullable();
            $table->text('meta_keyword')->nullable();
            $table->string('meta_description', 200)->nullable();
            $table->string('meta_author', 100)->nullable();
            $table->string('meta_title', 200)->nullable();
            $table->longText('script_head')->nullable();
            $table->longText('script_body')->nullable();
            $table->longText('custom_css')->nullable();
            $table->longText('custom_js')->nullable();
            $table->text('copyright_text')->nullable();
            $table->integer('office_working_day')->default(0);
            $table->string('mail_driver', 100)->nullable();
            $table->string('mail_host', 100)->nullable();
            $table->string('mail_port', 100)->nullable();
            $table->string('mail_username', 100)->nullable();
            $table->string('mail_password', 100)->nullable();
            $table->string('mail_encryption', 100)->nullable();
            $table->string('mail_from_name', 100)->nullable();
            $table->string('mail_from_address', 100)->nullable();
            $table->boolean('mail_status')->default(false);
            $table->string('sms_username', 50)->nullable();
            $table->string('sms_password', 50)->nullable();
            $table->string('sms_senderid', 50)->nullable();
            $table->string('sms_url_masking', 200)->nullable();
            $table->string('sms_url_nonmasking', 200)->nullable();
            $table->string('sms_type', 50)->nullable();
            $table->boolean('sms_status')->default(false);
            $table->string('fb_app_id', 100)->nullable();
            $table->string('fb_app_secret', 100)->nullable();
            $table->string('fb_app_redirect_url', 200)->nullable();
            $table->string('tw_app_id', 100)->nullable();
            $table->string('tw_app_secret', 100)->nullable();
            $table->string('tw_app_redirect_url', 200)->nullable();
            $table->string('google_client_id', 100)->nullable();
            $table->string('google_client_secret', 100)->nullable();
            $table->string('google_client_redirect_url', 200)->nullable();
            $table->string('facebook_link', 200)->nullable();
            $table->string('twitter_link', 200)->nullable();
            $table->string('instagram_link', 200)->nullable();
            $table->string('linkedin_link', 200)->nullable();
            $table->string('pinterest_link', 200)->nullable();
            $table->string('youtube_link', 200)->nullable();
            $table->string('currency', 10)->nullable();
            $table->integer('currency_decimal')->default(0)->comment('0=0,1=0.0,2=0.00');
            $table->integer('currency_position')->default(0)->comment('0=left, 1=right');
            $table->boolean('register_mail_user')->default(false);
            $table->boolean('register_mail_author')->default(false);
            $table->boolean('forget_password_mail_user')->default(false);
            $table->boolean('register_verify_mail_user')->default(false);
            $table->text('admin_mails')->nullable();
            $table->text('pi_terms_condition')->nullable();
            $table->boolean('register_sms_user')->default(false);
            $table->boolean('register_sms_author')->default(false);
            $table->boolean('forget_password_sms_user')->default(false);
            $table->boolean('register_verify_sms_user')->default(false);
            $table->text('admin_numbers')->nullable();
            $table->string('theme', 100)->nullable();
            $table->string('adminTheme', 100)->nullable();
            $table->decimal('balance', 20, 2)->default(0.00);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generals');
    }
};
