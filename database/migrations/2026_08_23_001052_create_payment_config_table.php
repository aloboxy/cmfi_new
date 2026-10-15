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
        Schema::create('payment_config', function (Blueprint $table) {
            $table->id();
            $table->string('paypal_username', 255)->nullable()->default(null);
            $table->string('paypal_password', 255)->nullable()->default(null);
            $table->string('paypal_signature', 255)->nullable()->default(null);
            $table->string('paypal_email', 255)->nullable()->default(null);
            $table->tinyInteger('paypal_sandbox')->nullable()->default(null);
            $table->tinyInteger('paypal_status')->nullable()->default(null);
            $table->string('stripe_secret', 255)->nullable()->default(null);
            $table->string('stripe_demo', 255)->nullable()->default(null);
            $table->tinyInteger('stripe_status')->nullable()->default(null);
            $table->string('payumoney_key', 255)->nullable()->default(null);
            $table->string('payumoney_salt', 255)->nullable()->default(null);
            $table->tinyInteger('payumoney_demo')->nullable()->default(null);
            $table->tinyInteger('payumoney_status')->nullable()->default(null);
            $table->string('paystack_secret_key', 255);
            $table->tinyInteger('paystack_status');
            $table->string('razorpay_key_id', 255);
            $table->string('razorpay_key_secret', 255);
            $table->tinyInteger('razorpay_demo');
            $table->tinyInteger('razorpay_status');
            $table->integer('branch_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_config');
    }
};
