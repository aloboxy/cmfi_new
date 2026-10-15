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
        Schema::create('live_class_config', function (Blueprint $table) {
            $table->id();
            $table->string('zoom_api_key', 255)->nullable()->default(null);
            $table->string('zoom_api_secret', 255);
            $table->integer('branch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_class_config');
    }
};
