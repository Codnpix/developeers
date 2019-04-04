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
        factory(App\CodeSnippet::class, 20)->create();
    }
}
