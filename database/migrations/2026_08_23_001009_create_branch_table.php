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
        Schema::create('branch', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->nullable()->default(null);
            $table->string('school_name', 255);
            $table->string('email', 100);
            $table->string('mobileno', 100);
            $table->string('currency', 100);
            $table->string('symbol', 25);
            $table->string('city', 255);
            $table->string('state', 255);
            $table->text('address');
            $table->tinyInteger('use_section')->default(1)->comment('1=use sections, 0=no sections');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch');
    }
};
