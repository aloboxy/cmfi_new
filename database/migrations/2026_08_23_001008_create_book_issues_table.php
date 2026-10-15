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
        Schema::create('book_issues', function (Blueprint $table) {
            $table->id();
            $table->integer('book_id');
            $table->integer('user_id');
            $table->integer('role_id');
            $table->date('date_of_issue')->nullable()->default(null);
            $table->date('date_of_expiry')->nullable()->default(null);
            $table->date('return_date')->nullable()->default(null);
            $table->decimal('fine_amount', 18, 2);
            $table->tinyInteger('status')->default(0)->comment('0 = pending, 1 = accepted, 2 = rejected, 3 = returned');
            $table->string('issued_by', 255)->nullable()->default(null);
            $table->integer('return_by')->nullable()->default(null);
            $table->integer('session_id');
            $table->integer('branch_id')->nullable()->default(null);
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_issues');
    }
};
