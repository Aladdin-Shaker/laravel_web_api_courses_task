<?php

namespace App\Http\Controllers\Course;

use App\Course;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class CourseController extends ApiController
{

    // get all courses available and disabled
    public function index()
    {
        $courses = Course::all();
        return $this->showAll($courses);
    }

    // get only available courses
    public function getAvailableCourses()
    {
        $courses = Course::where('status', 1)->get();
        return $this->showAll($courses);
    }

    // get only disabled courses
    public function getDisabledCourses()
    {
        $courses = Course::where('status', 0)->get();
        return $this->showAll($courses);
    }


    // add course
    public function store(Request $request)
    {
        $rules = [
            'title' => 'required|string|min:1|unique:courses',
            'start' => 'required|date|after:today',
            'end' => 'required|date|after:start',
        ];
        $this->validate($request, $rules);
        $data = $request->all();
        $data['status'] = $this->isAvailable($data['start'], $data['end']);

        if ($data['status'] == 0) {
            return $this->errorResponse('Invalid you should enter correct course date', 409);
        }

        $course = Course::create($data);
        return $this->showOne($course, 201);
    }

    public function show(Course $course)
    {
        return $this->showOne($course);
    }


    public function update(Request $request, Course $course)
    {
        $rules = [
            'title' => 'string|min:1|unique:courses,title,' . $course->id, // unique:table,column
            'start' => 'required|date|after:today',
            'end' => ' required|date|after:start',
        ];
        $this->validate($request, $rules);
        if ($request->has('title')) {
            $course->title = $request->title;
        }

        if ($request->has('start')) {
            $course->start = $request->start;
            $course->status = $this->isAvailable($request->start, $request->end);
        }

        if ($request->has('end')) {
            $course->end = $request->end;
            $course->status = $this->isAvailable($request->start, $request->end);
        }

        if ($course->status == 0) {
            return $this->errorResponse('Error, you should enter fact course date, end grater than start', 409);
        }

        if ($course->isClean()) {
            return $this->errorResponse('You need to specify a different value to update', 409);
        }

        $course->save();
        return $this->showOne($course);
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return $this->showOne($course);
    }

    // check course status
    public function isAvailable($start, $end)
    {
        $startDate = strtotime($start);
        $endDate = strtotime($end);
        // Get the difference as a number
        $diffDays = $endDate - $startDate;
        if ($diffDays > 0) {
            return 1;
        } else {
            return 0;
        }
    }
}
