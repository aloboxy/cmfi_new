<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $table = 'subject';

    protected $fillable = [
        'name',
        'subject_code',
        'subject_type',
        'subject_author',
        'branch_id',
    ];

    public $timestamps = false;

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function assignments()
    {
        return $this->hasMany(SubjectAssign::class, 'subject_id');
    }
}
