<?php

use App\Version;
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

$factory->define(Version::class, function (Faker $faker) {
    return [
        'number' => '1.' . rand(0,9),
        'text_content' => $faker->paragraph($nbSentences = 4, $variableNbSentences = true),
        'author_id' => rand(0,20),
        'snippets_id' => [],
        'comments_id' => [],
        'created_at' => now(),
        'updated_at' => now(),
    ];
});
