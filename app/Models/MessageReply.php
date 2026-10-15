<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageReply extends Model
{
    protected $table = 'message_reply';

    protected $fillable = [
        'message_id',
        'body',
        'file_name',
        'enc_name',
        'identity'
    ];

    public $timestamps = false;
}
