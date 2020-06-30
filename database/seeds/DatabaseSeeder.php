<?php

use App\Activity;
use App\Course;
use App\Role;
use App\Section;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        User::truncate();
        Course::truncate();
        Section::truncate();
        Activity::truncate();
        Role::truncate();
        DB::table('course_user')->truncate();
        DB::table('role_user')->truncate();

        $userQuantity = 500;
        $coursesQuantity = 200;
        $sectionsQuantity = 300;
        $activitiesQuantity = 500;
        $rolesQuantity = 2;

        $users = factory(User::class, $userQuantity)->create();
        factory(Course::class, $coursesQuantity)->create();
        factory(Section::class, $sectionsQuantity)->create();
        factory(Activity::class, $activitiesQuantity)->create();
        factory(Role::class, $rolesQuantity)->create();


        foreach ($users as $user) {
            $courses_ids = [];
            $courses_ids[] = Course::all()->where('status', 1)->random()->id;
            $courses_ids[] = Course::all()->where('status', 1)->random()->id;
            $courses_ids[] = Course::all()->where('status', 1)->random()->id;
            $user->courses()->sync($courses_ids);
        }

        foreach ($users as $user) {
            $roles_ids = [];
            $roles_ids[] = Role::all()->random()->id;

            $user->roles()->sync($roles_ids);
        }
    }
}
