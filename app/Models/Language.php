<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $table = 'languages';
    public $timestamps = false;
    protected $fillable = ['word','english','bengali','arabic','french','hindi','indonesian','italian','japanese','korean','dutch','portuguese','thai','turkish','urdu','chinese'];
}
