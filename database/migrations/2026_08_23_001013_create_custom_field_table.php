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
        Schema::create('custom_field', function (Blueprint $table) {
            $table->id();
            $table->string('form_to', 50)->nullable()->default(null);
            $table->string('field_label', 100);
            $table->text('default_value')->nullable();
            $table->enum('field_type', ['text','textarea','dropdown','date','checkbox','number','url','email']);
            $table->string('required', 5)->default('false');
            $table->tinyInteger('status')->default(1);
            $table->string('show_on_table', 5)->nullable()->default(null);
            $table->integer('field_order');
            $table->integer('bs_column');
            $table->integer('branch_id');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_field');
    }
};
