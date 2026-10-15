<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $table = 'exam';

    protected $fillable = [
        'name',
        'term_id',
        'type_id',
        'session_id',
        'branch_id',
        'remark',
        'mark_distribution'
    ];

    public $timestamps = true;
    protected $casts = ['mark_distribution' => 'array'];
}
