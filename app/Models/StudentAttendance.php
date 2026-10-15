<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model
{
    protected $table = 'student_attendance';

    protected $fillable = [
        'student_id',
        'date',
        'status',
        'remark',
        'branch_id'
    ];

    public $timestamps = true;
}
