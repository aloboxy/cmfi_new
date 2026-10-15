<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentModel extends Model
{
    protected $table = 'parent';

    protected $fillable = [
        'name',
        'relation',
        'father_name',
        'mother_name',
        'occupation',
        'income',
        'education',
        'email',
        'mobileno',
        'address',
        'city',
        'state',
        'branch_id',
        'photo',
        'facebook_url',
        'linkedin_url',
        'twitter_url',
        'active',
    ];

    /**
     * Relationship with Students
     */
    public function students()
    {
        return $this->hasMany(Student::class, 'parent_id');
    }

    /**
     * Relationship with Login Credentials
     */
    public function credential()
    {
        return $this->hasOne(User::class, 'user_id')->where('role', 6);
    }

    /**
     * Relationship with Branch
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
