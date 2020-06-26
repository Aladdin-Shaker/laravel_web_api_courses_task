<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// User
Route::get('users/verified', 'User\UserController@verified')->name('verified');;
Route::get('users/unverified', 'User\UserController@unverified')->name('unverified');;
Route::resource('users', 'User\UserController', ['except' => ['create', 'edit']]);
Route::resource('users.courses', 'User\UserCourseController',  ['only' => ['index', 'update']]);

// Course
Route::get('courses/available', 'Course\CourseController@getAvailableCourses')->name('available');
Route::get('courses/disabled', 'Course\CourseController@getDisabledCourses')->name('disable');
Route::resource('courses', 'Course\CourseController', ['except' => ['create', 'edit']]);
Route::resource('courses.sections', 'Course\CourseSectionController', ['except' => ['create', 'edit']]);
Route::resource('courses.users', 'Course\CourseUserController', ['only' => ['index', 'update', 'destroy']]);

// Section
Route::resource('sections', 'Section\SectionController', ['only' => ['index', 'show']]);
Route::resource('sections.activities', 'Section\SectionActivityController', ['except' => ['create', 'edit']]);

// Activity
Route::resource('activities', 'Activity\ActivityController', ['only' => ['index', 'show', 'destroy']]);
