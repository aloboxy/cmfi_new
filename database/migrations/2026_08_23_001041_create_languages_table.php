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
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('word', 255);
            $table->string('english', 255);
            $table->string('bengali', 255);
            $table->string('arabic', 255);
            $table->string('french', 255);
            $table->string('hindi', 255);
            $table->string('indonesian', 255);
            $table->string('italian', 255);
            $table->string('japanese', 255);
            $table->string('korean', 255);
            $table->string('dutch', 255);
            $table->string('portuguese', 255);
            $table->string('thai', 255);
            $table->string('turkish', 255);
            $table->string('urdu', 255);
            $table->string('chinese', 255);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
};
