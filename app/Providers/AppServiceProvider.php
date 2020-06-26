<?php

namespace App\Providers;

use App\Course;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // check if the course start equal to end course end make status zero
        Course::updated(function ($course) {
            $startDate = strtotime($course->start);
            $endDate = strtotime($course->end);
            // Get the difference as anumber
            $diffDays = $endDate - $startDate;
            if ($diffDays > 0) {
                $course->status = 0;
                $course->save();
            }
        });
    }
}
