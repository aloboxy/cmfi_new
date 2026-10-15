<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HostelCategory extends Model
{
    protected $table = 'hostel_category';
    protected $fillable = ['name','description','branch_id','type'];
}
