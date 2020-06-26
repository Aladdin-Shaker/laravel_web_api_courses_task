<?php

namespace App;

use App\User;
use App\Section;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use SoftDeletes;

    protected $dated = ['deleted_at'];
    protected $fillable = [
        'title',
        'start',
        'end',
        'status'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->using(UserCourse::class)
            ->withTimestamps()
            ->withPivot(['score']);
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }
}
