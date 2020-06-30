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

// Auth
Route::post('login', 'API\Auth\AuthController@login')->name('login');
Route::post('register', 'API\Auth\AuthController@register')->name('register');

Route::group(['middleware' => ['auth:api']], function () {
    Route::get('logout', 'API\Auth\AuthController@logout')->name('logout');;
    Route::get('user', 'API\Auth\AuthController@details')->name('user');;
});

// User
Route::get('users/verified', 'API\User\UserController@verified')->name('verified');;
Route::get('users/unverified', 'API\User\UserController@unverified')->name('unverified');;
Route::resource('users', 'API\User\UserController', ['except' => ['create', 'edit', 'store']]);
Route::resource('users.courses', 'API\User\UserCourseController',  ['only' => ['index', 'update']]);

// Course
Route::put('courses/{course}/users/{user}/enroll', 'API\Course\CourseUserController@enrollUser')->name('enrollUser');
Route::get('courses/available', 'API\Course\CourseController@getAvailableCourses')->name('available');
Route::get('courses/disabled', 'API\Course\CourseController@getDisabledCourses')->name('disable');
Route::resource('courses', 'API\Course\CourseController', ['except' => ['create', 'edit']]);
Route::resource('courses.sections', 'API\Course\CourseSectionController', ['except' => ['create', 'edit']]);
Route::resource('courses.users', 'API\Course\CourseUserController', ['only' => ['index', 'update', 'destroy']]);

// Section
Route::resource('sections', 'API\Section\SectionController', ['only' => ['index', 'show']]);
Route::resource('sections.activities', 'API\Section\SectionActivityController', ['except' => ['create', 'edit']]);

// Activity
Route::resource('activities', 'API\Activity\ActivityController', ['only' => ['index', 'show', 'destroy']]);
