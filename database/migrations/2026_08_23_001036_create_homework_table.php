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
        Schema::create('homework', function (Blueprint $table) {
            $table->id();
            $table->integer('class_id');
            $table->integer('section_id');
            $table->integer('session_id');
            $table->integer('subject_id');
            $table->date('date_of_homework');
            $table->date('date_of_submission');
            $table->text('description');
            $table->integer('created_by');
            $table->date('create_date');
            $table->string('status', 10);
            $table->tinyInteger('sms_notification');
            $table->date('schedule_date')->nullable()->default(null);
            $table->string('document', 255);
            $table->date('evaluation_date')->nullable()->default(null);
            $table->integer('evaluated_by');
            $table->integer('branch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homework');
    }
};
