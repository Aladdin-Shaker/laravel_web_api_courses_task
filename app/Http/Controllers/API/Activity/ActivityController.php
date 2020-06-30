<?php

namespace App\Http\Controllers\API\Activity;

use App\Activity;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class ActivityController extends ApiController
{

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('checkAdmin')->only('destroy');
    }

    // get all activities
    public function index()
    {
        $activities = Activity::all();
        return $this->showAll($activities);
    }

    // show specific activity
    public function show(Activity $activity)
    {
        return $this->showOne($activity);
    }

    // delete a specific activity
    public function destroy(Activity $activity)
    {
        $activity->delete();
        return $this->showOne($activity);
    }
}
