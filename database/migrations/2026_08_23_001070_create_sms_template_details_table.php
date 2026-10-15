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
        Schema::create('sms_template_details', function (Blueprint $table) {
            $table->id();
            $table->integer('template_id');
            $table->tinyInteger('notify_student')->default(1);
            $table->tinyInteger('notify_parent')->default(1);
            $table->longText('template_body');
            $table->integer('branch_id');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_template_details');
    }
};
