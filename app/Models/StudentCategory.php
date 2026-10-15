<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentCategory extends Model
{
    protected $table = 'student_category';
    public $timestamps = false;
    protected $fillable = ['name','branch_id'];
}
