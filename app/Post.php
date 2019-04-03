<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Post extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'posts';
    public $timestamps = true;

    protected $fillable = [
        'title', 'versions','comments'
    ];
}
