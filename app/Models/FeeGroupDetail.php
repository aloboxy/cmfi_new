<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeGroupDetail extends Model
{
    protected $table = 'fee_groups_details';
    public $timestamps = false;
    protected $fillable = ['fee_groups_id','fee_type_id','amount','due_date'];

    public function feeType()
    {
        return $this->belongsTo(FeesType::class, 'fee_type_id');
    }
    public function group()
    {
        return $this->belongsTo(FeeGroup::class, 'fee_groups_id');
    }
}
