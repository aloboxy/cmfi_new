<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubjectAssign extends Model
{
    protected $table = 'subject_assign';

    protected $fillable = [
        'class_id',
        'section_id',
        'subject_id',
        'teacher_id',
        'branch_id',
        'session_id',
    ];

    public $timestamps = true;

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

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

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
