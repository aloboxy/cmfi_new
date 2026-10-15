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
        Schema::create('global_settings', function (Blueprint $table) {
            $table->id();
            $table->string('institute_name', 255);
            $table->string('institution_code', 255);
            $table->string('reg_prefix', 255);
            $table->string('institute_email', 100);
            $table->text('address');
            $table->string('mobileno', 100);
            $table->string('currency', 100);
            $table->string('currency_symbol', 100);
            $table->string('sms_service_provider', 100);
            $table->integer('session_id');
            $table->string('translation', 100);
            $table->string('footer_text', 255);
            $table->string('animations', 100);
            $table->string('timezone', 100);
            $table->string('date_format', 100);
            $table->string('facebook_url', 255);
            $table->string('twitter_url', 255);
            $table->string('linkedin_url', 255);
            $table->string('youtube_url', 255);
            $table->string('cron_secret_key', 255)->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_settings');
    }
};
