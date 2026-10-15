<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookIssue extends Model
{
    protected $table = 'book_issues';
    public $timestamps = false;
    protected $fillable = ['book_id','user_id','role_id','date_of_issue','date_of_expiry','return_date','fine_amount','status','issued_by','return_by','session_id','branch_id'];

    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }
}
