<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveClass extends Model
{
    protected $table = 'live_class';

    protected $fillable = [
        'title',
        'meeting_id',
        'meeting_password',
        'class_id',
        'section_id',
        'remarks',
        'date',
        'start_time',
        'end_time',
        'created_by',
        'branch_id'
    ];

    public $timestamps = false;
    protected $casts = ['section_id' => 'array'];
}
