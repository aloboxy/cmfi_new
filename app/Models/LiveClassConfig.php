<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveClassConfig extends Model
{
    protected $table = 'live_class_config';
    public $timestamps = false;
    protected $fillable = ['zoom_api_key','zoom_api_secret','branch_id'];
}
