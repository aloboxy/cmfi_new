<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentConfig extends Model
{
    protected $table = 'payment_config';
    protected $fillable = ['paypal_username','paypal_password','paypal_signature','paypal_email','paypal_sandbox','paypal_status','stripe_secret','stripe_demo','stripe_status','payumoney_key','payumoney_salt','payumoney_demo','payumoney_status','paystack_secret_key','paystack_status','razorpay_key_id','razorpay_key_secret','razorpay_demo','razorpay_status','branch_id'];
}
