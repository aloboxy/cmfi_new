<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeePaymentHistory extends Model
{
    protected $table = 'fee_payment_history';
    public $timestamps = false;
    protected $fillable = ['allocation_id','type_id','collect_by','amount','discount','fine','pay_via','remarks','date'];
}
