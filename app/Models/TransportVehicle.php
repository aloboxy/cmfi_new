<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransportVehicle extends Model
{
    protected $table = 'transport_vehicle';
    protected $fillable = ['vehicle_no','capacity','insurance_renewal','driver_name','driver_phone','driver_license','branch_id'];
}
