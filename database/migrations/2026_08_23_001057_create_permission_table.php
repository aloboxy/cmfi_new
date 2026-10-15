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
        Schema::create('permission', function (Blueprint $table) {
            $table->id();
            $table->integer('module_id');
            $table->string('name', 100);
            $table->string('prefix', 100);
            $table->tinyInteger('show_view')->nullable()->default(1);
            $table->tinyInteger('show_add')->nullable()->default(1);
            $table->tinyInteger('show_edit')->nullable()->default(1);
            $table->tinyInteger('show_delete')->nullable()->default(1);
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission');
    }
};
