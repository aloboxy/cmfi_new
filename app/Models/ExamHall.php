<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamHall extends Model
{
    protected $table = 'exam_hall';

    protected $fillable = [
        'hall_no',
        'seats',
        'branch_id'
    ];

    public $timestamps = false;
}
