<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimetableClass extends Model
{
    protected $table = 'timetable_class';

    protected $fillable = [
        'class_id',
        'section_id',
        'break',
        'subject_id',
        'teacher_id',
        'class_room',
        'time_start',
        'time_end',
        'day',
        'session_id',
        'branch_id',
    ];

    public $timestamps = false;

    protected $casts = [
        'break' => 'boolean',
    ];

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Staff::class, 'teacher_id');
    }
}
