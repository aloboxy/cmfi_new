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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('account_id', 20);
            $table->integer('voucher_head_id');
            $table->string('type', 100);
            $table->string('category', 20);
            $table->string('ref', 255);
            $table->decimal('amount', 18, 2)->default(0.00);
            $table->decimal('dr', 18, 2)->default(0.00);
            $table->decimal('cr', 18, 2)->default(0.00);
            $table->decimal('bal', 18, 2)->default(0.00);
            $table->date('date');
            $table->string('pay_via', 20);
            $table->text('description');
            $table->string('attachments', 255);
            $table->integer('branch_id');
            $table->tinyInteger('system')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
