<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventType extends Model
{
    protected $table = 'event_types';

    protected $fillable = [
        'name',
        'icon',
        'branch_id'
    ];

    public $timestamps = false;
}
