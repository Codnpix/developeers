<?php

use App\Group;
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
function generateIdsArray($array) {
    $selectedIds = [];
    $scope = sizeof($array);
    for ($i = 1; $i < 5; $i++)
    {
        $index = rand(0, $scope - 1);
        $pick = $array[$index];
        unset($array[$index]);
        $array = array_values($array);
        array_push($selectedIds, $pick);
        $scope = sizeof($array);
    }
    return $selectedIds;
}

$factory->define(Group::class, function (Faker $faker) {
    $idsPool = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20];
    return [
        'name' => $faker->unique()->name,
        'description' => $faker->paragraph($nbSentences = 2, $variableNbSentences = true),
        'users_id' => generateIdsArray($idsPool),
        'keywords_id' => generateIdsArray($idsPool),
        'posts_id' => [],
        'votes' => [],
        'created_at' => now(),
        'updated_at' => now(),
    ];
});
