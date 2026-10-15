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
        Schema::create('fees_reminder', function (Blueprint $table) {
            $table->id();
            $table->string('frequency', 255);
            $table->string('days', 20);
            $table->text('message');
            $table->tinyInteger('student');
            $table->tinyInteger('guardian');
            $table->integer('branch_id');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fees_reminder');
    }
};
