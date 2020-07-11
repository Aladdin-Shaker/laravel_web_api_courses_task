<?php

namespace App\Http\Controllers\API\User;

use App\Course;
use App\Http\Controllers\ApiController;
use App\User;
use Illuminate\Http\Request;

class UserCourseController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('checkAdmin')->only('index');
        $this->middleware('checkTeacher')->except('index');
    }

    // get all courses related to specific student
    public function index(User $user)
    {
        $courses = $user->courses;
        if ($courses->isEmpty()) {
            return $this->showMessage('This student didnt enroll any course yet');
        }
        return $this->showAll($courses);
    }

    // if a teacher/admin want to update a specific student score for specific course
    public function update(Request $request, User $user, Course $course)
    {

        /* ($request->user()->roles->where('role',  'teacher') && $request->user()->courses->contains($course->id))
            || ($request->user()->roles->whereIn('role', ['admin'])) */

        $admin = $request->user()->roles->contains('role', 'admin');
        $teacher = $request->user()->roles->contains('role',  'teacher');
        $teacherWithCourse = $request->user()->courses->contains($course->id);

        if (($admin) or ($teacher && $teacherWithCourse)) {
            if ($user->roles->whereIn('role', 'student')->isNotEmpty()) {
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
            } else {
                return $this->errorResponse('This user is not a student', 401);
            }
        } else {
            return $this->errorResponse('Unauthorized teacher for this course', 401);
        }
    }
}
