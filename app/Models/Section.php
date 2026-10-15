<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $table = 'section';

    protected $fillable = [
        'name',
        'capacity',
        'branch_id',
    ];

    public $timestamps = false;

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function classes()
    {
        return $this->belongsToMany(SchoolClass::class, 'sections_allocation', 'section_id', 'class_id');
    }
}
