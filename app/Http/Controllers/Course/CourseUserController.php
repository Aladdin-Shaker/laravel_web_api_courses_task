<?php

namespace App\Http\Controllers\Course;

use App\Course;
use App\Http\Controllers\ApiController;
use App\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CourseUserController extends ApiController
{
    // get all students/users related to a specific course
    public function index(Course $course)
    {
        $users = $course->users;
        if ($users->isEmpty()) {
            return $this->showMessage('There are no students enrolled to this course');
        }
        return $this->showAll($users);
    }

    // add relationship between exist user and exist course M:M
    // if a user/student enrolled to this course OR assign a user/teacher to specific course
    public function update(Request $request, Course $course, User $user)
    {
        if ($course->status == 0) {
            return $this->errorResponse('Cannot enroll in this course, is not available currently', 404);
        }

        $this->checkEnroll($course, $user);

        $course->users()->syncWithoutDetaching([$user->id]);
        return $this->showAll($course->users);
    }

    // detach a relationship between course and user M:M
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
        if (count($user->courses()->where('id', $course->id)->get()) > 0) {
            throw new HttpException(422, 'The specific student already enrolled in this course');
        }
    }
}
