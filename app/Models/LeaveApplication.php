<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveApplication extends Model
{
    protected $table = 'leave_application';
    public $timestamps = false;
    protected $fillable = ['user_id','role_id','category_id','reason','start_date','end_date','leave_days','status','apply_date','approved_by','orig_file_name','enc_file_name','comments','session_id','branch_id'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'apply_date' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(LeaveCategory::class, 'category_id');
    }
}
