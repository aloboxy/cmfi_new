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
        Schema::create('transport_route', function (Blueprint $table) {
            $table->id();
            $table->longText('name');
            $table->longText('start_place');
            $table->longText('remarks');
            $table->longText('stop_place');
            $table->integer('branch_id')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_route');
    }
};
