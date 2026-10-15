<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('module_controls', function (Blueprint $table) {
            $table->id();
            $table->string('module_key', 100)->unique(); // e.g. 'student_details', 'parents'
            $table->string('display_name', 150);
            $table->string('icon', 50)->nullable();
            $table->string('parent_key', 100)->nullable()->index();
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('module_controls');
    }
};
