<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Post extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'posts';
    public $timestamps = true;

    protected $fillable = [
        'title', 'author_id', 'versions_id', 'keywords_id', 'votes', 'created_at', 'updated_at'
    ];
}
