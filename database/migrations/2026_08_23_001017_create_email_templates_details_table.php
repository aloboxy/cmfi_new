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
        Schema::create('email_templates_details', function (Blueprint $table) {
            $table->id();
            $table->integer('template_id');
            $table->string('subject', 255);
            $table->text('template_body');
            $table->tinyInteger('notified')->default(1);
            $table->integer('branch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_templates_details');
    }
};
