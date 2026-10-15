<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeesType extends Model
{
    protected $table = 'fees_type';
    public $timestamps = false;
    protected $fillable = ['name','fee_code','description','branch_id'];
}
