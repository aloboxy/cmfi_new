<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayslipDetail extends Model
{
    protected $table = 'payslip_details';
    public $timestamps = false;
    protected $fillable = ['payslip_id','name','amount','type'];

    public function payslip()
    {
        return $this->belongsTo(Payslip::class, 'payslip_id');
    }
}
