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
        Schema::create('payment_salary_stipend', function (Blueprint $table) {
            $table->id();
            $table->integer('payslip_id');
            $table->longText('name');
            $table->integer('amount');
            $table->string('type', 20);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_salary_stipend');
    }
};
