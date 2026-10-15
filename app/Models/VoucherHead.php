<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoucherHead extends Model
{
    protected $table = 'voucher_head';
    public $timestamps = false;
    protected $fillable = ['name','type','system','branch_id'];
}
