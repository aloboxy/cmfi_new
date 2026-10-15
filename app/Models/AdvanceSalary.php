<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvanceSalary extends Model
{
    protected $table = 'advance_salary';
    public $timestamps = false;
    protected $fillable = ['staff_id','amount','deduct_month','year','reason','request_date','paid_date','status','create_at','issued_by','comments','branch_id'];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
}
