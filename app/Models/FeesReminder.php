<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeesReminder extends Model
{
    protected $table = 'fees_reminder';
    public $timestamps = false;
    protected $fillable = ['frequency','days','message','student','guardian','branch_id'];
}
