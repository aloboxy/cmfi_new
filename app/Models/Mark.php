<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mark extends Model
{
    protected $table = 'mark';

    protected $fillable = [
        'student_id',
        'subject_id',
        'class_id',
        'section_id',
        'exam_id',
        'mark',
        'absent',
        'session_id',
        'branch_id'
    ];

    public $timestamps = false;
}
