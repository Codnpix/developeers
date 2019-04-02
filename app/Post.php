<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Post extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'developeers';

    protected $fillable = [
        'title', 'versions','comment'
    ];
}
