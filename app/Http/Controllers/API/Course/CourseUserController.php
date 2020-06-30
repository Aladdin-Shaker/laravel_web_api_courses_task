<?php

namespace App\Http\Controllers\API\Course;

use App\Course;
use App\Http\Controllers\ApiController;
use App\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CourseUserController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('checkAdmin')->only(['update', 'destroy']);
        $this->middleware('checkTeacher')->except(['enrollUser', 'update', 'destroy']);
    }

    // get all students/users related to a specific course => access controle :admin,teacher
    public function index(Course $course)
    {
        $users = $course->users;
        if ($users->isEmpty()) {
            return $this->showMessage('There are no students enrolled to this course');
        }
        return $this->showAll($users);
    }

    // if a user/student enrolled to this course  => access controle :user
    public function enrollUser(Course $course, User $user)
    {
        if ($course->status == 0) {
            return $this->errorResponse('Cannot enroll in this course, is not available currently', 404);
        }

        $this->checkEnroll($course, $user);

        $course->users()->syncWithoutDetaching([$user->id]);
        return $this->showAll($course->users);
    }

    //  assign a user/teacher to specific course  => access controle :admin
    public function update(Request $request, Course $course, User $user)
    {
        if ($course->status == 0) {
            return $this->errorResponse('Cannot assign a teacher to this course, is not available currently', 404);
        }

        $this->checkAssign($course, $user);

        $course->users()->syncWithoutDetaching([$user->id]);
        return $this->showAll($course->users);
    }

    // detach a relationship between course and user M:M  => access controle :admin
    public function destroy(Course $course, User $user)
    {
        if (!$course->users()->find($user->id)) {
            return $this->errorResponse('The specified student, does not have this course', 404);
        }

        $course->users()->detach($user->id);
        return $this->showAll($course->users);
    }

    // check if the student already have been enrolled to this course or not
    public function checkEnroll(Course $course, User $user)
    {
        // dd($user->roles->first()->role);

        if ($user->roles->first()->role === 'student') {
            if (count($user->courses()->where('id', $course->id)->get()) > 0) {
                throw new HttpException(422, 'The specific student already enrolled in this course');
            }
        } else {
            throw new HttpException(422, 'This user is not a student');
        }
    }

    // check if the teacher already have been assigned to this course or not
    public function checkAssign(Course $course, User $user)
    {
        if ($user->roles->first()->role === 'teacher') {
            if (count($user->courses()->where('id', $course->id)->get()) > 0) {
                throw new HttpException(422, 'The specific teacher already asigned in this course');
            }
        } else {
            throw new HttpException(422, 'This user is not a teacher');
        }
    }
}
