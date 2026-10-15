<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookCategory extends Model
{
    protected $table = 'book_category';
    public $timestamps = false;
    protected $fillable = ['name','branch_id'];
}
