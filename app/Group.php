<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Group extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'groups';
    public $timestamps = true;

    protected $fillable = [
        'name', 'desciption', 'keywords', 'users_id', 'created_at', 'updated_at'
    ];
}
