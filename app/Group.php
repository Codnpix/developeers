<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Group extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'developeers';
    public $timestamps = true;

    protected $fillable = [
        'name', 'desciption', 'posts_id', 'keywords_id', 'users_id', 'votes', 'created_at', 'updated_at'
    ];
}
