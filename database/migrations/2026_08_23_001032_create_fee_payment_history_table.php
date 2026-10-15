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
        Schema::create('fee_payment_history', function (Blueprint $table) {
            $table->id();
            $table->integer('allocation_id');
            $table->integer('type_id');
            $table->string('collect_by', 20)->nullable()->default(null);
            $table->decimal('amount', 18, 2);
            $table->decimal('discount', 18, 2);
            $table->decimal('fine', 18, 2);
            $table->string('pay_via', 20);
            $table->longText('remarks');
            $table->date('date')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_payment_history');
    }
};
