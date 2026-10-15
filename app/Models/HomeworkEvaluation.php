<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeworkEvaluation extends Model
{
    protected $table = 'homework_evaluation';

    protected $fillable = [
        'homework_id',
        'student_id',
        'remark',
        'rank',
        'date',
        'status'
    ];

    public $timestamps = false;
}
