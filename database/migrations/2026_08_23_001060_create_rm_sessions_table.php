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
        Schema::create('rm_sessions', function (Blueprint $table) {
            $table->string('id', 40);
            $table->string('ip_address', 45);
            $table->integer('timestamp')->default(0);
            $table->string('data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rm_sessions');
    }
};
