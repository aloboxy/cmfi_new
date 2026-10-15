<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomFieldValue extends Model
{
    protected $table = 'custom_fields_values';
    public $timestamps = false;
    protected $fillable = ['relid','field_id','value'];
}
