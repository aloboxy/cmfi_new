<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payslip extends Model
{
    protected $table = 'payslip';
    public $timestamps = false;
    protected $fillable = ['staff_id','month','year','basic_salary','total_allowance','total_deduction','net_salary','bill_no','remarks','pay_via','hash','paid_by','branch_id','created_at'];

    protected $casts = ['created_at' => 'datetime'];

    public function details()
    {
        return $this->hasMany(PayslipDetail::class, 'payslip_id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
}
