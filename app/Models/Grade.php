<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $table = 'grade';

    protected $fillable = [
        'name',
        'grade_point',
        'lower_mark',
        'upper_mark',
        'remark',
        'branch_id'
    ];

    public $timestamps = false;
}
