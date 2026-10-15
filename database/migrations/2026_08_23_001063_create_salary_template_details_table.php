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
        Schema::create('salary_template_details', function (Blueprint $table) {
            $table->id();
            $table->string('salary_template_id', 20);
            $table->string('name', 200);
            $table->decimal('amount', 18, 2)->default(0.00);
            $table->tinyInteger('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_template_details');
    }
};
