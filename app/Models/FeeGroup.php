<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeGroup extends Model
{
    protected $table = 'fee_groups';
    public $timestamps = false;
    protected $fillable = ['name','description','session_id','branch_id'];

    public function details()
    {
        return $this->hasMany(FeeGroupDetail::class, 'fee_groups_id');
    }
}
