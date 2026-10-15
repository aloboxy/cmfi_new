<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Homework extends Model
{
    protected $table = 'homework';

    protected $fillable = [
        'class_id',
        'section_id',
        'session_id',
        'subject_id',
        'date_of_homework',
        'date_of_submission',
        'description',
        'created_by',
        'create_date',
        'status',
        'sms_notification',
        'schedule_date',
        'document',
        'evaluation_date',
        'evaluated_by',
        'branch_id'
    ];

    public $timestamps = false;
}
