<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $table = 'attachments';

    protected $fillable = [
        'title',
        'remarks',
        'type_id',
        'uploader_id',
        'class_id',
        'file_name',
        'enc_name',
        'subject_id',
        'session_id',
        'date',
        'branch_id'
    ];

    public $timestamps = true;
}
