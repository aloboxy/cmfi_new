<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffBankAccount extends Model
{
    protected $table = 'staff_bank_account';
    protected $fillable = ['staff_id','bank_name','holder_name','bank_branch','bank_address','ifsc_code','account_no'];
}
