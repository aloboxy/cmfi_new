<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'event';

    protected $fillable = [
        'title',
        'remark',
        'status',
        'type',
        'audition',
        'selected_list',
        'start_date',
        'end_date',
        'created_by',
        'branch_id'
    ];

    public $timestamps = true;
    protected $casts = ['selected_list' => 'array'];
}
