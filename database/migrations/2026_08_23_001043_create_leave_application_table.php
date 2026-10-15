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
        Schema::create('leave_application', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('role_id');
            $table->integer('category_id');
            $table->longText('reason');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('leave_days', 20)->default(0);
            $table->integer('status')->default(1)->comment('1=pending,2=accepted 3=rejected');
            $table->timestamp('apply_date')->useCurrent();
            $table->integer('approved_by');
            $table->string('orig_file_name', 255);
            $table->string('enc_file_name', 255);
            $table->string('comments', 255);
            $table->integer('session_id')->nullable()->default(null);
            $table->integer('branch_id')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_application');
    }
};
