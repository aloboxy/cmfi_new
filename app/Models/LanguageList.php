<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LanguageList extends Model
{
    protected $table = 'language_list';
    protected $fillable = ['name','lang_field','status'];
}
