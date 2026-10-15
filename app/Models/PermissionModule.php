<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionModule extends Model
{
    protected $table = 'permission_modules';
    public $timestamps = false;
    protected $fillable = ['name','prefix','system','sorted'];
}
