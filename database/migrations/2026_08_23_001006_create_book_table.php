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
        Schema::create('book', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('cover', 255)->nullable()->default(null);
            $table->string('author', 255);
            $table->string('isbn_no', 255);
            $table->integer('category_id');
            $table->string('publisher', 255);
            $table->string('edition', 255);
            $table->date('purchase_date');
            $table->text('description');
            $table->decimal('price', 18, 2);
            $table->string('total_stock', 20);
            $table->string('issued_copies', 20)->default(0);
            $table->integer('branch_id')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book');
    }
};
