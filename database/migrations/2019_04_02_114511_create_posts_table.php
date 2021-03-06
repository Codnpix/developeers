<?php

use Illuminate\Support\Facades\Schema;
use Jenssegers\Mongodb\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
    * The name of the database connection to use.
    *
    * @var string
    */
    protected $connection = 'mongodb';

    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::connection($this->connection)
        ->table('posts', function (Blueprint $collection)
        {
            $collection->string('title');
            $collection->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection($this->connection)
        ->table('posts', function (Blueprint $collection)
        {
            $collection->drop();
        });
    }
}
