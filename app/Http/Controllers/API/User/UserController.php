<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\User;

class UserController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('checkAdmin')->except(['update']);
    }

    // show all users verified and unverified
    public function index()
    {
        $users = User::all();
        return $this->showAll($users);
    }

    // show only verified users
    public function verified()
    {
        $users = User::where('verified', 1)->get();
        return $this->showAll($users);
    }

    // show only unverified users
    public function unverified()
    {
        $users = User::where('verified', 0)->get();
        return $this->showAll($users);
    }

    // show specific user
    public function show(User $user)
    {
        return $this->showOne($user, 200); // implicit model binding
    }

    // update specific user
    public function update(Request $request, User $user)
    {
        // dd($user->id, $request->user()->id);
        // dd($request->header("Authorization"));
        $rules = [
            'name' => 'string',
            'email' => 'email|unique:users,email,' . $user->id, // unique except the own user email
            'password' => 'min:6|confirmed',
            'image' => 'image|mimes:jpeg,png,gif,webp|max:2048'
        ];

        if ($user->id === $request->user()->id) {
            $this->validate($request, $rules);
            if ($request->has('name')) {
                $user->name = $request->name;
            }

            if ($request->has('email') && $user->email !== $request->email) {
                $user->verified = User::UNVERIFIED_USER;
                $user->email_verified_at = null;
                $user->email = $request->email;
            }

            if ($request->has('password')) {
                $user->password = bcrypt($request->password);
            }

            if ($request->has('image')) {
                Storage::delete($user->image);
                $user->image = $request->image->store('');
            }

            if (!$user->isDirty()) {
                return $this->errorResponse('You need to specify a different value to update', 409);
            }
            $user->save();
            return $this->showOne($user);
        } else {
            return $this->errorResponse('Unauthorized to update this user', 401);
        }
    }

    // delete specific user
    public function destroy(User $user)
    {
        $user->delete();
        Storage::delete($user->image);
        return $this->showOne($user);
    }

    // mutators & accessors

    public function setNameAttribute($name)
    {
        $this->attributes['name'] = strtolower($name);
    }

    public function getNameAttribute($name)
    {
        $this->ucwords($name);
    }

    public function setEmailAttribute($email)
    {
        $this->attributes['email'] = strtolower($email);
    }
}
