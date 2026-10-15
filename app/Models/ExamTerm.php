<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamTerm extends Model
{
    protected $table = 'exam_term';

    protected $fillable = [
        'name',
        'branch_id',
        'session_id'
    ];

    public $timestamps = false;
}
