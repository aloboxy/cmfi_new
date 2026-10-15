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
        Schema::create('live_class', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('meeting_id', 255);
            $table->string('meeting_password', 255);
            $table->integer('class_id');
            $table->string('section_id', 11);
            $table->text('remarks');
            $table->date('date');
            $table->string('start_time');
            $table->string('end_time');
            $table->integer('created_by');
            $table->integer('branch_id');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_class');
    }
};
