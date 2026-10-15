<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hostel extends Model
{
    protected $table = 'hostel';
    protected $fillable = ['name','category_id','address','watchman','remarks','branch_id'];
}
