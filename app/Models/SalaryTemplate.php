<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryTemplate extends Model
{
    protected $table = 'salary_template';
    public $timestamps = false;
    protected $fillable = ['name','basic_salary','overtime_salary','branch_id'];

    public function details()
    {
        return $this->hasMany(SalaryTemplateDetail::class, 'salary_template_id');
    }

    public function allowances()
    {
        return $this->hasMany(SalaryTemplateDetail::class, 'salary_template_id')->where('type', 1);
    }

    public function deductions()
    {
        return $this->hasMany(SalaryTemplateDetail::class, 'salary_template_id')->where('type', 2);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
