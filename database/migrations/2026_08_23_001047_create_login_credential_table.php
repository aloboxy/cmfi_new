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
        Schema::create('login_credential', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('username', 100);
            $table->string('password', 250);
            $table->tinyInteger('role');
            $table->tinyInteger('active')->default(1)->comment('1(active) 0(deactivate)');
            $table->dateTime('last_login')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_credential');
    }
};
