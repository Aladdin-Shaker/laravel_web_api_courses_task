<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserCourse extends Pivot
{
    protected $hidden = ['user_id', 'course_id', 'created_at', 'updated_at'];
    protected $fillable = [
        'score', 'user_id', 'course_id'
    ];
}
