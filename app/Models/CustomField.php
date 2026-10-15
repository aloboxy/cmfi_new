<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomField extends Model
{
    protected $table = 'custom_field';
    public $timestamps = false;
    protected $fillable = ['form_to','field_label','default_value','field_type','required','status','show_on_table','field_order','bs_column','branch_id'];
}
