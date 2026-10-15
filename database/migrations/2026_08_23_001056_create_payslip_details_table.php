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
        Schema::create('payslip_details', function (Blueprint $table) {
            $table->id();
            $table->integer('payslip_id');
            $table->string('name', 255);
            $table->decimal('amount', 18, 2);
            $table->tinyInteger('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payslip_details');
    }
};
