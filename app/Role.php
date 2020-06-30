<?php

namespace App;

use App\User;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes;

    protected $dated = ['deleted_at'];

    protected $fillable = ['role'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
