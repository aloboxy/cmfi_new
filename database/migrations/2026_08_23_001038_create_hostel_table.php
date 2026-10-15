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
        Schema::create('hostel', function (Blueprint $table) {
            $table->id();
            $table->longText('name');
            $table->integer('category_id');
            $table->longText('address');
            $table->longText('watchman');
            $table->longText('remarks')->nullable();
            $table->integer('branch_id')->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hostel');
    }
};
