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
        Schema::create('homework_evaluation', function (Blueprint $table) {
            $table->id();
            $table->integer('homework_id');
            $table->integer('student_id');
            $table->text('remark');
            $table->integer('rank');
            $table->date('date');
            $table->string('status', 100);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homework_evaluation');
    }
};
