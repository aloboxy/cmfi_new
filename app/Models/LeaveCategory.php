<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveCategory extends Model
{
    protected $table = 'leave_category';
    public $timestamps = false;
    protected $fillable = ['name','role_id','days','branch_id'];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
