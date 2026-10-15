<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentDocument extends Model
{
    protected $table = 'student_documents';
    protected $fillable = ['student_id','title','type','remarks','file_name','enc_name'];
}
