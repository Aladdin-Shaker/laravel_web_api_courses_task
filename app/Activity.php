<?php

namespace App;

use App\Section;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use SoftDeletes;

    protected $dated = ['deleted_at'];
    protected $hidden = ['section'];
    protected $fillable = [
        'title',
        'description',
        'videoURL',
        'section_id'
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
