<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffPrivilege extends Model
{
    protected $table = 'staff_privileges';
    public $timestamps = false;
    protected $fillable = ['role_id','permission_id','is_add','is_edit','is_view','is_delete'];
}
