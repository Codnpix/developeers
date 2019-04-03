<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Group extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'groups';
    public $timestamps = true;

    protected $fillable = [
        'name', 'desciption', 'posts_id', 'keywords_id', 'users_id', 'votes', 'created_at', 'updated_at'
    ];
}
