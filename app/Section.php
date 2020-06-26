<?php

namespace App;

use App\Activity;
use App\Course;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends Model
{
    use SoftDeletes;

    protected $dated = ['deleted_at'];
    protected $hidden = ['course'];

    protected $fillable = [
        'title',
        'description',
        'course_id'
    ];

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
