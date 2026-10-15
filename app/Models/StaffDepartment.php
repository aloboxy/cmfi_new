<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffDepartment extends Model
{
    protected $table = 'staff_department';
    protected $fillable = ['name','branch_id'];
}
