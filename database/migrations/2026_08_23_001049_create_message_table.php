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
        Schema::create('message', function (Blueprint $table) {
            $table->id();
            $table->longText('body');
            $table->string('subject', 255);
            $table->text('file_name')->nullable();
            $table->text('enc_name')->nullable();
            $table->tinyInteger('trash_sent');
            $table->integer('trash_inbox');
            $table->tinyInteger('fav_inbox');
            $table->tinyInteger('fav_sent');
            $table->string('reciever', 100);
            $table->string('sender', 100);
            $table->tinyInteger('read_status')->default(0)->comment('0 unread 1 read');
            $table->tinyInteger('reply_status')->default(0)->comment('0 unread 1 read');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message');
    }
};
