<?php

namespace App\Http\Controllers\Section;

use App\Section;
use App\Activity;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SectionActivityController extends ApiController
{

    // get all activities related to specific section
    public function index(Section $section)
    {
        $activities = $section->activities;
        if ($activities->isEmpty()) {
            return $this->showMessage('There are no activities in this section');
        }
        return $this->showAll($activities);
    }

    // add new activity related to specific section
    public function store(Request $request, Section $section)
    {
        $rules = [
            'title' => 'required|string|min:5:max:50',
            'description' => 'required|string|min:5',
            'videoURL' => 'url'
        ];

        $this->validate($request, $rules);
        $data = $request->all();
        $data['section_id'] = $section->id;
        $activity = Activity::create($data);
        return $this->showOne($activity, 201);
    }

    // show specific activity from specific section
    public function show(Section $section, Activity $activity)
    {
        $this->checkCourse($section, $activity);
        $data = $section->activities->where('id', $activity->id);
        return $this->showAll($data);
    }

    // update a specific activity accordding to specific section
    public function update(Request $request, Section $section, Activity $activity)
    {
        $rules = [
            'title' => 'string|min:5:max:50',
            'description' => 'string|min:5',
            'videoURL' => 'url'
        ];
        $this->validate($request, $rules);
        $this->checkCourse($section, $activity);
        $activity->fill($request->only(['title', 'description', 'videoURL']));
        if ($request->has('title')) {
            $activity->title = $request->title;
        }

        if ($request->has('description')) {
            $activity->description = $request->description;
        }

        if ($request->has('videoURL')) {
            $activity->videoURL = $request->videoURL;
        }

        if ($activity->isClean()) {
            return $this->errorResponse('You need to specify a different value to update', 422);
        }

        $activity->save();
        return $this->showOne($activity);
    }

    // delete specific activity from specific section
    public function destroy(Section $section, Activity $activity)
    {
        $this->checkCourse($section, $activity);
        $activity->delete();
        return $this->showOne($activity);
    }

    // check if the id as PK from Section table is equal to the same id as FK from Activity table
    public function checkCourse(Section $section, Activity $activity)
    {
        if ($section->id != $activity->section->id) {
            throw new HttpException(422, 'The specific section does not have this activity ');
        }
    }
}
