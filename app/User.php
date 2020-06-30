<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Course;
use App\Events\UserCreatedEvent;
use App\Role;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, SoftDeletes;

    const VERIFIED_USER = '1';
    const UNVERIFIED_USER = '0';

    protected $table = 'users';
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'image', 'verified'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'pivot'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];



    public function courses()
    {
        return $this->belongsToMany(Course::class)
            ->using(UserCourse::class)
            ->withTimestamps()
            ->withPivot(['score']);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    // check verified user
    public function isVerified()
    {
        return $this->verified === User::VERIFIED_USER;
    }

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => UserCreatedEvent::class,
    ];
}
