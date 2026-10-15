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
        Schema::create('student', function (Blueprint $table) {
            $table->id();
            $table->string('register_no', 100);
            $table->string('admission_date', 100);
            $table->string('first_name', 255);
            $table->string('last_name', 255);
            $table->string('gender', 20);
            $table->string('birthday', 100)->nullable()->default(null);
            $table->string('religion', 100);
            $table->string('caste', 100);
            $table->string('blood_group', 100);
            $table->string('mother_tongue', 100)->nullable()->default(null);
            $table->text('current_address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->string('city', 255);
            $table->string('state', 255);
            $table->string('mobileno', 100);
            $table->integer('category_id')->default(0);
            $table->string('email', 100);
            $table->integer('parent_id');
            $table->integer('route_id')->default(0);
            $table->integer('vehicle_id')->default(0);
            $table->integer('hostel_id')->default(0);
            $table->integer('room_id')->default(0);
            $table->text('previous_details');
            $table->string('photo', 100)->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student');
    }
};
