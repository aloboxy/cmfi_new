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
        Schema::create('staff_bank_account', function (Blueprint $table) {
            $table->id();
            $table->integer('staff_id');
            $table->string('bank_name', 200);
            $table->string('holder_name', 255);
            $table->string('bank_branch', 255);
            $table->string('bank_address', 255);
            $table->string('ifsc_code', 200);
            $table->string('account_no', 200);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_bank_account');
    }
};
