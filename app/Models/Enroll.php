<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enroll extends Model
{
    protected $table = 'enroll';
    public $timestamps = false;
    protected $fillable = [
        'student_id','class_id','section_id','roll','session_id','branch_id',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
    public function classe()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }
    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }
}
