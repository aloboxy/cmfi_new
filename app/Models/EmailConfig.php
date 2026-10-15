<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailConfig extends Model
{
    protected $table = 'email_config';
    public $timestamps = false;
    protected $fillable = ['email','protocol','smtp_host','smtp_user','smtp_pass','smtp_port','smtp_encryption','branch_id'];
}
