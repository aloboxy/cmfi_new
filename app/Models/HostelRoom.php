<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HostelRoom extends Model
{
    protected $table = 'hostel_room';
    public $timestamps = false;
    protected $fillable = ['name','hostel_id','no_beds','category_id','bed_fee','remarks','branch_id'];

    public function hostel()
    {
        return $this->belongsTo(Hostel::class, 'hostel_id');
    }
}
