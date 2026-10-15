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
        Schema::create('parent', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('relation', 255);
            $table->string('father_name', 255)->nullable()->default(null);
            $table->string('mother_name', 255)->nullable()->default(null);
            $table->string('occupation', 100);
            $table->string('income', 100);
            $table->string('education', 255);
            $table->string('email', 100);
            $table->string('mobileno', 100);
            $table->text('address');
            $table->string('city', 255);
            $table->string('state', 255);
            $table->integer('branch_id')->nullable()->default(null);
            $table->string('photo', 255);
            $table->string('facebook_url', 255)->nullable()->default(null);
            $table->string('linkedin_url', 255)->nullable()->default(null);
            $table->string('twitter_url', 255)->nullable()->default(null);
            $table->tinyInteger('active')->default(0)->comment('0(active) 1(deactivate)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parent');
    }
};
