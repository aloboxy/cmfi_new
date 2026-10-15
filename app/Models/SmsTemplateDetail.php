<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsTemplateDetail extends Model
{
    protected $table = 'sms_template_details';
    public $timestamps = false;
    protected $fillable = ['template_id','notify_student','notify_parent','template_body','branch_id'];
}
