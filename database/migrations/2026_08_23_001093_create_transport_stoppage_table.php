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
        Schema::create('transport_stoppage', function (Blueprint $table) {
            $table->id();
            $table->string('stop_position', 255);
            $table->string('stop_time');
            $table->decimal('route_fare', 18, 2);
            $table->integer('branch_id');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_stoppage');
    }
};
