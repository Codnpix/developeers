<?php

use Illuminate\Database\Seeder;
use App\Keyword;

class KeywordsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Keyword::create(['word' => 'api']);
        Keyword::create(['word' => 'application']);
        Keyword::create(['word' => 'php']);
        Keyword::create(['word' => 'framework']);
        Keyword::create(['word' => 'javascript']);
        Keyword::create(['word' => 'html']);
        Keyword::create(['word' => 'css']);
        Keyword::create(['word' => 'sass']);
        Keyword::create(['word' => 'xml']);
        Keyword::create(['word' => 'json']);
        Keyword::create(['word' => 'sql']);
        Keyword::create(['word' => 'mysql']);
        Keyword::create(['word' => 'bdd']);
        Keyword::create(['word' => 'mongodb']);
        Keyword::create(['word' => 'react']);
        Keyword::create(['word' => 'vue']);
        Keyword::create(['word' => 'angular']);
        Keyword::create(['word' => 'scss']);
        Keyword::create(['word' => 'bootstrap']);
        Keyword::create(['word' => 'materialize']);
        Keyword::create(['word' => 'symfony']);
        Keyword::create(['word' => 'java']);
        Keyword::create(['word' => 'c']);
        Keyword::create(['word' => 'c++']);
        Keyword::create(['word' => 'python']);
        Keyword::create(['word' => 'django']);
        Keyword::create(['word' => 'oauth']);
    }
}
