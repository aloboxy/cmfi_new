<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'student';

    protected $fillable = [
        'register_no',
        'admission_date',
        'first_name',
        'last_name',
        'gender',
        'birthday',
        'religion',
        'caste',
        'blood_group',
        'mother_tongue',
        'current_address',
        'permanent_address',
        'city',
        'state',
        'mobileno',
        'category_id',
        'email',
        'parent_id',
        'route_id',
        'vehicle_id',
        'hostel_id',
        'room_id',
        'previous_details',
        'photo',
    ];

    protected $casts = [
        'admission_date' => 'date',
        'birthday' => 'date',
        'previous_details' => 'array',
    ];

    /**
     * Get the student's full name.
     */
    public function getNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Relationship with Parent
     */
    public function guardian()
    {
        return $this->belongsTo(ParentModel::class, 'parent_id');
    }

    /**
     * Relationship with Login Credentials
     */
    public function credential()
    {
        return $this->hasOne(User::class, 'user_id')->where('role', 7);
    }

    public function enroll()
    {
        return $this->hasOne(Enroll::class, 'student_id');
    }

    public function enrolls()
    {
        return $this->hasMany(Enroll::class, 'student_id');
    }

    public function category()
    {
        return $this->belongsTo(StudentCategory::class, 'category_id');
    }

    public function documents()
    {
        return $this->hasMany(StudentDocument::class, 'student_id');
    }
}
