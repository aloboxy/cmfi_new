<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffAttendance extends Model
{
    protected $table = 'staff_attendance';

    protected $fillable = [
        'staff_id',
        'status',
        'remark',
        'date',
        'branch_id'
    ];

    public $timestamps = false;
}
