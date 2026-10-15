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
        Schema::create('bulk_sms_email', function (Blueprint $table) {
            $table->id();
            $table->string('campaign_name', 255)->nullable()->default(null);
            $table->string('sms_gateway', 55)->nullable()->default(0);
            $table->text('message')->nullable();
            $table->string('email_subject', 255)->nullable()->default(null);
            $table->tinyInteger('message_type')->nullable()->default(0)->comment('sms=1, email=2');
            $table->tinyInteger('recipient_type')->comment('group=1, individual=2, class=3');
            $table->longText('recipients_details')->nullable();
            $table->longText('additional')->nullable();
            $table->dateTime('schedule_time')->useCurrent();
            $table->tinyInteger('posting_status')->comment('schedule=1,competed=2');
            $table->integer('total_thread');
            $table->integer('successfully_sent');
            $table->integer('branch_id')->nullable()->default(null);
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulk_sms_email');
    }
};
