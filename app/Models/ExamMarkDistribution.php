<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamMarkDistribution extends Model
{
    protected $table = 'exam_mark_distribution';

    protected $fillable = [
        'name',
        'branch_id'
    ];

    public $timestamps = false;
}
