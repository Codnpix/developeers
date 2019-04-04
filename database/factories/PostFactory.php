<?php

use App\Post;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

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

$factory->define(Post::class, function (Faker $faker) {

    return [
        'title' => str_random(20),
        'author_id' => rand(0,20),
        'group_id' => '',
        'keywords' => [],
        'votes' => [],
        'created_at' => now(),
        'updated_at' => now(),
    ];
});
