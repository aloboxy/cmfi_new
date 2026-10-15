<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsApi extends Model
{
    protected $table = 'sms_api';
    public $timestamps = false;
    protected $fillable = ['name'];
}
