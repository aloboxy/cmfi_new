<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherAllocation extends Model
{
    protected $table = 'teacher_allocation';

    protected $fillable = [
        'class_id',
        'section_id',
        'teacher_id',
        'session_id',
        'branch_id',
    ];

    public $timestamps = false;

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Staff::class, 'teacher_id');
    }
}
