<?php

use Illuminate\Database\Seeder;

class VersionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Version::class, 40)->create();
    }
}
