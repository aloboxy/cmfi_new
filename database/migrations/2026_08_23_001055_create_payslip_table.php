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
        Schema::create('payslip', function (Blueprint $table) {
            $table->id();
            $table->integer('staff_id');
            $table->string('month', 200)->nullable()->default(null);
            $table->string('year', 20);
            $table->decimal('basic_salary', 18, 2);
            $table->decimal('total_allowance', 18, 2);
            $table->decimal('total_deduction', 18, 2);
            $table->decimal('net_salary', 18, 2);
            $table->string('bill_no', 25);
            $table->text('remarks');
            $table->tinyInteger('pay_via');
            $table->string('hash', 200)->nullable()->default(null);
            $table->string('paid_by', 200)->nullable()->default(null);
            $table->integer('branch_id')->nullable()->default(null);
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payslip');
    }
};
