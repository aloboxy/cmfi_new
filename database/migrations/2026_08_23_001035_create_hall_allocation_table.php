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
        Schema::create('hall_allocation', function (Blueprint $table) {
            $table->id();
            $table->integer('student_id');
            $table->integer('hall_no');
            $table->integer('class_id');
            $table->integer('section_id');
            $table->integer('exam_id');
            $table->integer('branch_id');
            $table->integer('session_id');
            $table->timestamp('timestamp')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hall_allocation');
    }
};
