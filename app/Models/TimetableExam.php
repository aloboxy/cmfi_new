<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimetableExam extends Model
{
    protected $table = 'timetable_exam';

    protected $fillable = [
        'exam_id',
        'class_id',
        'section_id',
        'subject_id',
        'time_start',
        'time_end',
        'mark_distribution',
        'hall_id',
        'exam_date',
        'branch_id',
        'session_id',
    ];

    public $timestamps = true;

    protected $casts = [
        'exam_date' => 'date',
    ];

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
}
