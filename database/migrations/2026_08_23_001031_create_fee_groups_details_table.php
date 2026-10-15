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
        Schema::create('fee_groups_details', function (Blueprint $table) {
            $table->id();
            $table->integer('fee_groups_id');
            $table->integer('fee_type_id');
            $table->decimal('amount', 18, 2);
            $table->date('due_date')->nullable()->default(null);
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_groups_details');
    }
};
