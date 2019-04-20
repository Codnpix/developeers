<?php

use Illuminate\Support\Facades\Schema;
use Jenssegers\Mongodb\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserDataTable extends Migration
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
        ->table('user_data', function (Blueprint $collection)
        {
            $collection->bigInteger('user_id')->unique();
            $collection->string('user_name');
            $collection->string('user_presentation');
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
        ->table('user_data', function (Blueprint $collection)
        {
            $collection->drop();
        });
    }
}
