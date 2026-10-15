<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'login_credential';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'username',
        'password',
        'role',
        'active',
        'last_login',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Disable standard timestamps since table uses created_at/updated_at as datetime fields manually
     */
    public $timestamps = true;

    /**
     * Get the password for the user (Auth override).
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the username/email for login (Auth override).
     */
    public function getAuthIdentifierName()
    {
        return 'username';
    }

    /**
     * Dynamically retrieve the associated profile (Student, Parent, or Staff)
     */
    public function getProfileAttribute()
    {
        if ($this->role == 7) {
            return $this->student;
        } elseif ($this->role == 6) {
            return $this->parent;
        } else {
            return $this->staff;
        }
    }

    /**
     * Relationships
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'user_id');
    }

    public function parent()
    {
        return $this->belongsTo(ParentModel::class, 'user_id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'user_id');
    }
}
