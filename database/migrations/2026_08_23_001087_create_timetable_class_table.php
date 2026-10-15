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
        Schema::create('timetable_class', function (Blueprint $table) {
            $table->id();
            $table->integer('class_id');
            $table->integer('section_id');
            $table->string('break', 11)->nullable()->default('false');
            $table->integer('subject_id');
            $table->integer('teacher_id');
            $table->string('class_room', 100)->nullable()->default(null);
            $table->string('time_start');
            $table->string('time_end');
            $table->string('day', 20);
            $table->integer('session_id');
            $table->integer('branch_id')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timetable_class');
    }
};
