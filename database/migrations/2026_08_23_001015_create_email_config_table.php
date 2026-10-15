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
        Schema::create('email_config', function (Blueprint $table) {
            $table->id();
            $table->string('email', 255)->nullable()->default(null);
            $table->string('protocol', 255);
            $table->string('smtp_host', 255)->nullable()->default(null);
            $table->string('smtp_user', 255)->nullable()->default(null);
            $table->string('smtp_pass', 255)->nullable()->default(null);
            $table->string('smtp_port', 100)->nullable()->default(null);
            $table->string('smtp_encryption', 10)->nullable()->default(null);
            $table->integer('branch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_config');
    }
};
