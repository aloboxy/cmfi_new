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
        Schema::create('transport_vehicle', function (Blueprint $table) {
            $table->id();
            $table->longText('vehicle_no');
            $table->longText('capacity');
            $table->longText('insurance_renewal');
            $table->longText('driver_name');
            $table->longText('driver_phone');
            $table->longText('driver_license');
            $table->integer('branch_id')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_vehicle');
    }
};
