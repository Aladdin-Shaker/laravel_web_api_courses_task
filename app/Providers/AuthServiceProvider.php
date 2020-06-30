<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();
        Passport::tokensExpireIn(Carbon::now()->addMinutes(30));
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(30));
        Passport::enableImplicitGrant();
        // define scope
        Passport::tokensCan([
            'student' => 'list available courses,
                          join a course,
                          view all sections and activities of this course',
            'teacher' => 'list all students of his course only,
                          update the student score',
            'admin'   => 'students management,
                          teachers management,
                          courses management,
                          sections management,
                          activities management,
                          assign a teacher to a specific course,
                          list all enrolled students of each course'
        ]);

        // default scope
        Passport::setDefaultScope(['student']);
    }
}
