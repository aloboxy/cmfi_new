<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeAllocation extends Model
{
    protected $table = 'fee_allocation';
    public $timestamps = false;
    protected $fillable = ['student_id','group_id','branch_id','session_id'];
}
