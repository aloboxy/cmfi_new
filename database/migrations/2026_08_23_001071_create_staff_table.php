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
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('staff_id', 25);
            $table->string('name', 255);
            $table->integer('department');
            $table->string('qualification', 255);
            $table->integer('designation');
            $table->string('joining_date', 100);
            $table->string('birthday', 100);
            $table->string('sex', 20);
            $table->string('religion', 100);
            $table->string('blood_group', 20);
            $table->text('present_address');
            $table->text('permanent_address');
            $table->string('mobileno', 100);
            $table->string('email', 100);
            $table->integer('salary_template_id')->nullable()->default(0);
            $table->integer('branch_id')->nullable()->default(null);
            $table->string('photo', 255)->nullable()->default(null);
            $table->string('facebook_url', 255)->nullable()->default(null);
            $table->string('linkedin_url', 255)->nullable()->default(null);
            $table->string('twitter_url', 255)->nullable()->default(null);
            $table->string('hash', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
