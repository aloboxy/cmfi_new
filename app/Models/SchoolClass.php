<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    protected $table = 'class';

    protected $fillable = [
        'name',
        'name_numeric',
        'next_class_id',
        'branch_id',
    ];

    public $timestamps = true;

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function sections()
    {
        return $this->belongsToMany(Section::class, 'sections_allocation', 'class_id', 'section_id');
    }

    public function sectionAllocations()
    {
        return $this->hasMany(SectionAllocation::class, 'class_id');
    }

    public function nextClass()
    {
        return $this->belongsTo(self::class, 'next_class_id');
    }
}
