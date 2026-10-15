<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsCredential extends Model
{
    protected $table = 'sms_credential';
    protected $fillable = ['sms_api_id','field_one','field_two','field_three','field_four','branch_id','is_active'];
}
