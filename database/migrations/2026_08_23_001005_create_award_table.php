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
        Schema::create('award', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->integer('user_id');
            $table->integer('role_id')->nullable()->default(null);
            $table->string('gift_item', 255);
            $table->decimal('award_amount', 18, 2);
            $table->text('award_reason');
            $table->date('given_date');
            $table->integer('session_id');
            $table->integer('branch_id')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('award');
    }
};
