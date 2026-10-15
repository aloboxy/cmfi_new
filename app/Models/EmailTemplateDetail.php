<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplateDetail extends Model
{
    protected $table = 'email_templates_details';
    public $timestamps = false;
    protected $fillable = ['template_id','subject','template_body','notified','branch_id'];
}
