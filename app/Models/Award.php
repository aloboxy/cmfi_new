<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Award extends Model
{
    protected $table = 'award';
    public $timestamps = false;
    protected $fillable = ['name','user_id','role_id','gift_item','award_amount','award_reason','given_date','session_id','branch_id'];

    protected $casts = ['given_date' => 'date'];
}
