<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransportStoppage extends Model
{
    protected $table = 'transport_stoppage';
    public $timestamps = false;
    protected $fillable = ['stop_position','stop_time','route_fare','branch_id'];
}
