<?php

use Illuminate\Database\Seeder;

class CodeSnippetsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Group::class, 20)->create();
    }
}
