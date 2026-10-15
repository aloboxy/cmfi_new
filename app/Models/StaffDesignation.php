<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffDesignation extends Model
{
    protected $table = 'staff_designation';
    protected $fillable = ['name','branch_id'];
}
