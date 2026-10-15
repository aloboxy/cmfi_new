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
        Schema::create('fee_fine', function (Blueprint $table) {
            $table->id();
            $table->integer('group_id');
            $table->integer('type_id');
            $table->string('fine_value', 20);
            $table->string('fine_type', 20);
            $table->string('fee_frequency', 20);
            $table->integer('branch_id');
            $table->integer('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_fine');
    }
};
