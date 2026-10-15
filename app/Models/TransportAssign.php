<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransportAssign extends Model
{
    protected $table = 'transport_assign';
    public $timestamps = false;
    protected $fillable = ['route_id','stoppage_id','vehicle_id','branch_id'];
}
