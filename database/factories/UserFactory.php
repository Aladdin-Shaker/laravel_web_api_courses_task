<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use App\Course;
use App\Activity;
use App\Role;
use App\Section;

use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token' => Str::random(10),
        'image' => $faker->randomElement(['1.jpg', '2.jpg', '3.jpg']),
        'verified' => $faker->randomElement([User::VERIFIED_USER, User::UNVERIFIED_USER]),
    ];
});

$factory->define(Course::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence,
        'start' => date('d-m-Y', rand(strtotime("Jan 01 2018"), strtotime("Nov 01 2019"))),
        'end' =>   date('d-m-Y', rand(strtotime("Jan 01 2019"), strtotime("Nov 01 2020"))),
        'status' => $faker->randomElement([0, 1]),
    ];
});

$factory->define(Section::class, function (Faker $faker) {
    return [
        'title' => $faker->word,
        'description' => $faker->paragraph(1),
        'course_id' => Course::all()->random()->id
    ];
});

$factory->define(Activity::class, function (Faker $faker) {
    return [
        'title' => $faker->word,
        'description' => $faker->paragraph(1),
        'videoURL' => $faker->url,
        'section_id' => Section::all()->random()->id
    ];
});

$factory->define(Role::class, function (Faker $faker) {
    return [
        'name' => $faker->randomElement(['teacher', 'student'])
    ];
});
