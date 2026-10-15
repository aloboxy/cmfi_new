<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $table = 'book';
    protected $fillable = ['title','cover','author','isbn_no','category_id','publisher','edition','purchase_date','description','price','total_stock','issued_copies','branch_id'];
}
