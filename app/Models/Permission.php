<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'permission';
    public $timestamps = false;
    protected $fillable = ['module_id','name','prefix','show_view','show_add','show_edit','show_delete'];
}
