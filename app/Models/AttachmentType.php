<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttachmentType extends Model
{
    protected $table = 'attachments_type';

    protected $fillable = [
        'name',
        'branch_id'
    ];

    public $timestamps = false;
}
