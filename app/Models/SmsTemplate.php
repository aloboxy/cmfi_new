<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsTemplate extends Model
{
    protected $table = 'sms_template';
    public $timestamps = false;
    protected $fillable = ['name','tags'];
}
