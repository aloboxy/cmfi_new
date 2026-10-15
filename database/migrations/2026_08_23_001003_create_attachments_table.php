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
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->text('remarks');
            $table->integer('type_id');
            $table->string('uploader_id', 20);
            $table->string('class_id', 20)->nullable()->default('unfiltered');
            $table->string('file_name', 255);
            $table->string('enc_name', 255);
            $table->string('subject_id', 200)->nullable()->default('unfiltered');
            $table->integer('session_id');
            $table->date('date');
            $table->integer('branch_id')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
