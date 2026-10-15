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
        Schema::create('salary_template', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->decimal('basic_salary', 18, 2);
            $table->string('overtime_salary', 100)->default(0);
            $table->tinyInteger('branch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_template');
    }
};
