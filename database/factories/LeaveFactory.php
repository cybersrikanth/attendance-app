<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Leave;
use Faker\Generator as Faker;
use Illuminate\Http\UploadedFile;

$factory->define(Leave::class, function (Faker $faker) use ($factory) {
    return [
        "student_id" => $factory->create(App\User::class)->id,
        "reason" => $faker->paragraph,
        "attachments" =>  [],
        "startDate" => date("Y-m-1"),
        "endDate" => date("Y-m-t")
    ];
});
