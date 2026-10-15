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
        Schema::create('timetable_exam', function (Blueprint $table) {
            $table->id();
            $table->integer('exam_id');
            $table->integer('class_id');
            $table->integer('section_id');
            $table->integer('subject_id');
            $table->string('time_start', 20);
            $table->string('time_end', 20);
            $table->text('mark_distribution');
            $table->integer('hall_id');
            $table->date('exam_date');
            $table->integer('branch_id');
            $table->integer('session_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timetable_exam');
    }
};
