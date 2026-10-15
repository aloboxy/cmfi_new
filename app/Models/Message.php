<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'message';

    protected $fillable = [
        'body',
        'subject',
        'file_name',
        'enc_name',
        'trash_sent',
        'trash_inbox',
        'fav_inbox',
        'fav_sent',
        'reciever',
        'sender',
        'read_status',
        'reply_status'
    ];

    public $timestamps = true;
}
