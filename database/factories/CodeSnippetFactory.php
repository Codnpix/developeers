<?php

use App\CodeSnippet;
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

$factory->define(CodeSnippet::class, function (Faker $faker) {
    return [
        'content' => $faker->paragraph($nbSentences = 4, $variableNbSentences = true),
        'language' => 'HTML',
        'version_id' => ''
    ];
});
