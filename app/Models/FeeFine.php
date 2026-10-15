<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeFine extends Model
{
    protected $table = 'fee_fine';
    public $timestamps = false;
    protected $fillable = ['group_id','type_id','fine_value','fine_type','fee_frequency','branch_id','session_id'];
}
