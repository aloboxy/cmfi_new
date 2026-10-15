<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryTemplateDetail extends Model
{
    protected $table = 'salary_template_details';
    public $timestamps = false;
    protected $fillable = ['salary_template_id','name','amount','type'];

    public function template()
    {
        return $this->belongsTo(SalaryTemplate::class, 'salary_template_id');
    }
}
