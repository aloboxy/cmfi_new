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
        Schema::create('exam', function (Blueprint $table) {
            $table->id();
            $table->longText('name');
            $table->integer('term_id')->nullable()->default(null);
            $table->tinyInteger('type_id')->comment('1=mark,2=gpa,3=both');
            $table->integer('session_id');
            $table->integer('branch_id')->nullable()->default(null);
            $table->text('remark');
            $table->text('mark_distribution');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam');
    }
};
