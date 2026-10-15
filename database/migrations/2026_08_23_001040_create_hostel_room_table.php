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
        Schema::create('hostel_room', function (Blueprint $table) {
            $table->id();
            $table->longText('name');
            $table->integer('hostel_id');
            $table->integer('no_beds');
            $table->integer('category_id')->nullable()->default(null);
            $table->decimal('bed_fee', 18, 2);
            $table->longText('remarks');
            $table->integer('branch_id')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hostel_room');
    }
};
