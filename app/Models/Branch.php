<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $table = 'branch';

    protected $fillable = [
        'name',
        'school_name',
        'email',
        'mobileno',
        'currency',
        'symbol',
        'city',
        'state',
        'address',
        'use_section',
    ];

    /**
     * Relationship with Students
     */
    public function students()
    {
        return $this->hasManyThrough(Student::class, ParentModel::class, 'branch_id', 'parent_id');
    }

    /**
     * Relationship with Staff
     */
    public function staff()
    {
        return $this->hasMany(Staff::class, 'branch_id');
    }
}
