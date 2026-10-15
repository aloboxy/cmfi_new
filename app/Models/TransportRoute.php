<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransportRoute extends Model
{
    protected $table = 'transport_route';
    public $timestamps = false;
    protected $fillable = ['name','start_place','stop_place','remarks','branch_id'];
}
