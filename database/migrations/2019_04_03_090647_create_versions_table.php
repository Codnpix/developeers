<?php

use Illuminate\Support\Facades\Schema;
use Jenssegers\Mongodb\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVersionsTable extends Migration
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
        ->table('versions', function (Blueprint $collection)
        {
            $collection->string('number');
            $collection->string('text_content');
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
        ->table('versions', function (Blueprint $collection)
        {
            $collection->drop();
        });
    }
}
