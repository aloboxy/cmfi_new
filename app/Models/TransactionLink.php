<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionLink extends Model
{
    protected $table = 'transactions_links';
    public $timestamps = false;
    protected $fillable = ['status','deposit','expense','branch_id'];
}
