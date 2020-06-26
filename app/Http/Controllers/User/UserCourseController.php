<?php

namespace App\Http\Controllers\User;

use App\Course;
use App\Http\Controllers\ApiController;
use App\User;
use Illuminate\Http\Request;

class UserCourseController extends ApiController
{
    // get all courses related to specific student
    public function index(User $user)
    {
        $courses = $user->courses;
        if ($courses->isEmpty()) {
            return $this->showMessage('This student didnt enroll any course yet');
        }
        return $this->showAll($courses);
    }


    // add relationship between exist user and exist course M:M and add score value
    // if a teacher want to update a specific student score for specific course
    public function update(Request $request, User $user, Course $course)
    {
        $rules = [
            'score' => 'required|numeric|min:0|max:100'
        ];

        $this->validate($request, $rules);

        if (!$course->users()->find($user->id)) {
            return $this->errorResponse('The specified student did not enroll this course', 404);
        }

        if ($course->status == 0) {
            return $this->errorResponse('Selected Course, is not available currently', 404);
        }

        $user->courses()->syncWithoutDetaching([$course->id => ['score' => $request->score]]);
        return $this->showAll($user->courses);
    }
}
