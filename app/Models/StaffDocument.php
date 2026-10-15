<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffDocument extends Model
{
    protected $table = 'staff_documents';
    protected $fillable = ['staff_id','title','category_id','remarks','file_name','enc_name'];
}
