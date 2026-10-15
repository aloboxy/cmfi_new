<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $table = 'staff';

    protected $fillable = [
        'staff_id',
        'name',
        'department',
        'qualification',
        'designation',
        'joining_date',
        'birthday',
        'sex',
        'religion',
        'blood_group',
        'present_address',
        'permanent_address',
        'mobileno',
        'email',
        'salary_template_id',
        'branch_id',
        'photo',
        'facebook_url',
        'linkedin_url',
        'twitter_url',
        'hash',
    ];

    protected $casts = [
        'joining_date' => 'date',
        'birthday' => 'date',
    ];

    /**
     * Relationship with Login Credentials
     */
    public function credential()
    {
        return $this->hasOne(User::class, 'user_id')->whereNotIn('role', [6, 7]);
    }

    /**
     * Relationship with Branch
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function departmentRel()
    {
        return $this->belongsTo(StaffDepartment::class, 'department');
    }

    public function designationRel()
    {
        return $this->belongsTo(StaffDesignation::class, 'designation');
    }

    public function documents()
    {
        return $this->hasMany(StaffDocument::class, 'staff_id');
    }

    public function bankAccount()
    {
        return $this->hasOne(StaffBankAccount::class, 'staff_id');
    }
}
