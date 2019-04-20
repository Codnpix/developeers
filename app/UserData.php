<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Model;

class UserData extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'notifications';
    public $timestamps = true;
}
