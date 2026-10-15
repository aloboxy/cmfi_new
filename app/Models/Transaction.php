<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';
    protected $fillable = ['account_id','voucher_head_id','type','category','ref','amount','dr','cr','bal','date','pay_via','description','attachments','branch_id','system'];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
    public function voucherHead()
    {
        return $this->belongsTo(VoucherHead::class, 'voucher_head_id');
    }
}
