<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SectionAllocation extends Model
{
    protected $table = 'sections_allocation';

    protected $fillable = [
        'class_id',
        'section_id',
    ];

    public $timestamps = false;

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }
}
