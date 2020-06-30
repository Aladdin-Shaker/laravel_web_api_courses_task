<?php

namespace App\Http\Controllers\API\Course;

use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\Course;
use App\Section;

class CourseSectionController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth:api')->except('index', 'show');
        $this->middleware('checkAdmin')->only(['store', 'update', 'destroy']);
    }

    // get all sections releted to specific course
    public function index(Course $course)
    {
        $sections = $course->sections;
        if ($sections->isEmpty()) {
            return $this->showMessage('There are no sections in this course');
        }
        return $this->showAll($sections);
    }

    // add new section for specific course
    public function store(Request $request, Course $course)
    {
        $rules = [
            'title' => 'required|string|min:3|unique:sections',
            'description' => 'required|min:5|max:300',
        ];

        $this->validate($request, $rules);
        $data = $request->all();
        $data['course_id'] = $course->id;

        $section = Section::create($data);
        return $this->showOne($section, 201);
    }

    // show specific section from specific course
    public function show(Course $course, Section $section)
    {
        $this->checkCourse($section, $course);
        $data = $course->sections->where('id', $section->id);
        return $this->showAll($data);
    }

    // update a specific section contained in specific course
    public function update(Request $request, Course $course, Section $section)
    {
        $rules = [
            'title' => 'string|min:3|unique:sections,title,' . $section->id, // unique:table,column',
            'description' => 'min:5|max:300',
        ];

        $this->validate($request, $rules);
        $this->checkCourse($section, $course);
        $section->fill($request->only(['title', 'description']));

        if ($request->has('title')) {
            $section->title = $request->title;
        }

        if ($request->has('description')) {
            $section->description = $request->description;
        }

        if ($section->isClean()) {
            return $this->errorResponse('You need to specify a different value to update', 422);
        }

        $section->save();
        return $this->showOne($section);
    }

    // delete specific section from specific course
    public function destroy(Course $course, Section $section)
    {
        $this->checkCourse($section, $course);
        $section->delete();
        return $this->showOne($section);
    }

    // check if the id as PK from Course table is equal to the same id as FK from section table
    public function checkCourse(Section $section, Course $course)
    {
        if ($course->id != $section->course->id) {
            throw new HttpException(422, 'The specific course does not have this section ');
        }
    }
}
