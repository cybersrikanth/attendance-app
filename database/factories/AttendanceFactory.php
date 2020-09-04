<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Attendance;
use Faker\Generator as Faker;

$factory->define(Attendance::class, function (Faker $faker) use ($factory) {
    return [
        "student_id" => $factory->create(App\User::class)->id,
        "teacher_id" =>  $factory->create(App\User::class)->id,
        "state" => Attendance::STATES[rand(0, 1)],
        "attendance_for" => date("Y-m-d")
    ];
});
