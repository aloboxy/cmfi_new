<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolYear extends Model
{
    protected $table = 'schoolyear';
    protected $fillable = ['school_year','created_by'];
}
