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
        Schema::create('advance_salary', function (Blueprint $table) {
            $table->id();
            $table->integer('staff_id');
            $table->decimal('amount', 18, 2);
            $table->string('deduct_month', 20)->nullable()->default(null);
            $table->string('year', 20);
            $table->text('reason')->nullable();
            $table->timestamp('request_date')->useCurrent();
            $table->string('paid_date', 20);
            $table->tinyInteger('status')->default(0)->comment('1=pending,2=paid,3=rejected');
            $table->dateTime('create_at')->useCurrent();
            $table->string('issued_by', 200)->nullable()->default(null);
            $table->string('comments', 255)->nullable()->default(null);
            $table->integer('branch_id')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advance_salary');
    }
};
