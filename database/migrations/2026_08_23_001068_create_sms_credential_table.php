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
        Schema::create('sms_credential', function (Blueprint $table) {
            $table->id();
            $table->integer('sms_api_id');
            $table->string('field_one', 300);
            $table->string('field_two', 300);
            $table->string('field_three', 300);
            $table->string('field_four', 300);
            $table->integer('branch_id');
            $table->tinyInteger('is_active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_credential');
    }
};
